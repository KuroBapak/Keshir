#include <ArduinoJson.h>
#include <LiquidCrystal_I2C.h>
#include <MFRC522.h>
#include <SPI.h>
#include <WebSocketsClient.h>
#include <WiFi.h>
#include <Wire.h>
#include <esp_task_wdt.h>

// ================= KONFIGURASI JARINGAN =================
const char *ssid = "Rhome_Multi";
const char *password = "mmtari9999";

const char *wss_host = "mqtt.kurobapak.site";
const int wss_port = 443;
const char *wss_path = "/mqtt";

const char *mqtt_user = "ESP32";
const char *mqtt_pass = "sudomoreno";

// Keshir Namespace
String device_id = "front_door";
String mqtt_topic_pub_tap = "keshir/attendance/" + device_id + "/up/tap";
String mqtt_topic_pub_event = "keshir/attendance/" + device_id + "/up/event";
String mqtt_topic_sub_cmd = "keshir/attendance/" + device_id + "/down/cmd";
String mqtt_topic_sub_response =
    "keshir/attendance/" + device_id + "/down/response";
String mqtt_topic_sub_broadcast = "keshir/attendance/broadcast/down/cmd";

#define WDT_TIMEOUT 15

// ================= PIN DEFINITIONS =================
#define SS_PIN 5
#define RST_PIN 27 // Diubah ke 27 untuk menghindari bentrok I2C (22)
#define BUZZER_PIN 4
#define LED_GREEN 2
#define LED_RED 15

// ================= OBJECTS =================
WebSocketsClient webSocket;
LiquidCrystal_I2C lcd(0x27, 16, 2);
MFRC522 rfid(SS_PIN, RST_PIN);

// ================= VARS GLOBAL =================
bool isMqttConnected = false;
bool wssLogPrinted = false;
unsigned long lastPing = 0;
unsigned long lastWifiCheck = 0;
unsigned long stateTimer = 0;

enum SystemState {
  IDLE,
  PROCESSING,
  DISPLAY_RESULT,
  REGISTER_MODE,
  REGISTER_PROCESSING
};
SystemState currentState = IDLE;

// ================= MQTT PACKETS BUILDER =================
void sendMqttConnect() {
  String clientId = "keshir_att_" + device_id;
  uint8_t varHeader[] = {0x00, 0x04, 'M',  'Q',  'T',
                         'T',  0x04, 0xC2, 0x00, 0x3C};
  uint16_t clientLen = clientId.length();
  uint16_t userLen = strlen(mqtt_user);
  uint16_t passLen = strlen(mqtt_pass);
  size_t remainingLen =
      sizeof(varHeader) + (2 + clientLen) + (2 + userLen) + (2 + passLen);
  uint8_t packet[256];
  int idx = 0;
  packet[idx++] = 0x10;
  size_t len = remainingLen;
  do {
    uint8_t d = len % 128;
    len /= 128;
    if (len > 0)
      d |= 0x80;
    packet[idx++] = d;
  } while (len > 0);
  for (int i = 0; i < sizeof(varHeader); i++)
    packet[idx++] = varHeader[i];
  packet[idx++] = (clientLen >> 8);
  packet[idx++] = (clientLen & 0xFF);
  for (int i = 0; i < clientLen; i++)
    packet[idx++] = clientId[i];
  packet[idx++] = (userLen >> 8);
  packet[idx++] = (userLen & 0xFF);
  for (int i = 0; i < userLen; i++)
    packet[idx++] = mqtt_user[i];
  packet[idx++] = (passLen >> 8);
  packet[idx++] = (passLen & 0xFF);
  for (int i = 0; i < passLen; i++)
    packet[idx++] = mqtt_pass[i];
  webSocket.sendBIN(packet, idx);
}

void sendMqttSubscribe(const char *topic) {
  String t = topic;
  size_t rLen = 5 + t.length();
  uint8_t packet[128];
  int idx = 0;
  packet[idx++] = 0x82;
  packet[idx++] = rLen;
  packet[idx++] = 0x00;
  packet[idx++] = 0x01;
  packet[idx++] = (t.length() >> 8);
  packet[idx++] = (t.length() & 0xFF);
  for (int i = 0; i < t.length(); i++)
    packet[idx++] = t[i];
  packet[idx++] = 0x00;
  webSocket.sendBIN(packet, idx);
}

void sendMqttPublish(const char *topic, const char *payload) {
  String t = topic;
  String p = payload;
  size_t rLen = 2 + t.length() + p.length();
  uint8_t packet[512];
  int idx = 0;
  packet[idx++] = 0x30;
  size_t len = rLen;
  do {
    uint8_t d = len % 128;
    len /= 128;
    if (len > 0)
      d |= 0x80;
    packet[idx++] = d;
  } while (len > 0);
  packet[idx++] = (t.length() >> 8);
  packet[idx++] = (t.length() & 0xFF);
  for (int i = 0; i < t.length(); i++)
    packet[idx++] = t[i];
  for (int i = 0; i < p.length(); i++)
    packet[idx++] = p[i];
  webSocket.sendBIN(packet, idx);
}

// ================= HARDWARE FEEDBACK =================
void drawScreen(String line1, String line2) {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(line1);
  lcd.setCursor(0, 1);
  lcd.print(line2);
}

void beep(int count, int duration) {
  for (int i = 0; i < count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(duration);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < count - 1)
      delay(100);
  }
}

// ================= WEBSOCKET EVENT HANDLER =================
void webSocketEvent(WStype_t type, uint8_t *payload, size_t length) {
  switch (type) {
  case WStype_DISCONNECTED:
    isMqttConnected = false;
    wssLogPrinted = (WiFi.status() != WL_CONNECTED);
    drawScreen("WiFi/MQTT", "Disconnected");
    break;
  case WStype_CONNECTED:
    Serial.println("[WSS] Connected! Sending MQTT Connect...");
    sendMqttConnect();
    break;
  case WStype_BIN:
    if (length > 0) {
      if ((payload[0] & 0xF0) == 0x20) {
        isMqttConnected = true;
        Serial.println("[MQTT] Authorized!");
        sendMqttSubscribe(mqtt_topic_sub_cmd.c_str());
        sendMqttSubscribe(mqtt_topic_sub_response.c_str());
        sendMqttSubscribe(mqtt_topic_sub_broadcast.c_str());
        drawScreen("Keshir POS", "Tap kartu anda");
        currentState = IDLE;
      } else if ((payload[0] & 0xF0) == 0x30) {
        int h = 2;
        if ((payload[1] & 0x80) != 0)
          h = 3;
        int tLen =
            ((unsigned char)payload[h] << 8) | (unsigned char)payload[h + 1];
        String topic = "";
        for (int i = h + 2; i < h + 2 + tLen; i++)
          topic += (char)payload[i];
        String msg = "";
        for (int i = h + 2 + tLen; i < length; i++)
          msg += (char)payload[i];

        StaticJsonDocument<256> doc;
        DeserializationError err = deserializeJson(doc, msg);
        if (!err) {
          if (topic == mqtt_topic_sub_cmd ||
              topic == mqtt_topic_sub_broadcast) {
            String action = doc["action"];
            if (action == "register_mode") {
              currentState = REGISTER_MODE;
              drawScreen("# Daftar Kartu", "Tap kartu baru..");
            } else if (action == "cancel_register") {
              currentState = IDLE;
              drawScreen("Keshir POS", "Tap kartu anda");
            } else if (action == "register_success") {
              String name = doc["name"];
              drawScreen("V Terdaftar!", name + " - OK");
              digitalWrite(LED_GREEN, HIGH);
              beep(1, 200);
              delay(2000);
              digitalWrite(LED_GREEN, LOW);
              currentState = IDLE;
              drawScreen("Keshir POS", "Tap kartu anda");
            }
          } else if (topic == mqtt_topic_sub_response) {
            String status = doc["status"];
            String name = doc["name"];

            if (status == "check_in") {
              String time = doc["time"];
              String statusIn = doc["status_in"];
              String line2 = "In " + time;
              if (statusIn == "late") {
                line2 = time + " [TELAT]";
              } else if (statusIn == "on_time") {
                line2 = time + " [TEPAT]";
              }

              drawScreen("Hai " + name, line2);
              digitalWrite(LED_GREEN, HIGH);
              beep(1, 100);
            } else if (status == "check_out") {
              String duration = doc["duration"];
              String time = doc["time"];
              drawScreen("Bye " + name, "Out " + time + " (" + duration + ")");
              digitalWrite(LED_GREEN, HIGH);
              beep(2, 100);
            } else if (status == "already_done") {
              drawScreen("- " + name, "Shift selesai");
              digitalWrite(LED_RED, HIGH);
              beep(2, 50);
            } else if (status == "cooldown") {
              int remaining = doc["remaining"];
              drawScreen("! " + name, "Tunggu " + String(remaining) + " mnt");
              digitalWrite(LED_RED, HIGH);
              beep(1, 500);
            } else if (status == "unknown_card") {
              drawScreen("X Kartu Asing", "Hubungi Manager");
              digitalWrite(LED_RED, HIGH);
              beep(3, 100);
            }

            delay(3000);
            digitalWrite(LED_GREEN, LOW);
            digitalWrite(LED_RED, LOW);
            drawScreen("Keshir POS", "Tap kartu anda");
            currentState = IDLE;
          }
        }
      }
    }
    break;
  }
}

// ================= SETUP =================
void setup() {
  Serial.begin(115200);

  // FIX I2C HANG (Dari referensi sistem AMCS)
  Wire.begin();
  Wire.setTimeOut(1000);

  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  pinMode(LED_RED, OUTPUT);

  // Initialize LCD
  lcd.begin();
  lcd.backlight();
  drawScreen("Booting...", "Connecting WiFi");

  // Initialize SPI bus & MFRC522
  SPI.begin();
  rfid.PCD_Init();

  esp_task_wdt_deinit();
  esp_task_wdt_config_t wdt_config = {.timeout_ms = WDT_TIMEOUT * 1000,
                                      .idle_core_mask =
                                          (1 << portNUM_PROCESSORS) - 1,
                                      .trigger_panic = true};
  esp_task_wdt_init(&wdt_config);
  esp_task_wdt_add(NULL);

  WiFi.begin(ssid, password);
}

// ================= MAIN LOOP =================
void loop() {
  esp_task_wdt_reset();
  unsigned long now = millis();

  if (now - lastWifiCheck > 5000) {
    lastWifiCheck = now;
    if (WiFi.status() != WL_CONNECTED) {
      if (!wssLogPrinted) {
        Serial.println("WiFi Lost...");
        wssLogPrinted = true;
      }
    } else {
      wssLogPrinted = false;
    }
  }

  webSocket.loop();

  static bool wssStarted = false;
  if (WiFi.status() == WL_CONNECTED && !wssStarted) {
    webSocket.beginSSL(wss_host, wss_port, wss_path, "", "mqtt");
    webSocket.setExtraHeaders("Origin: https://mqtt.kurobapak.site");
    webSocket.onEvent(webSocketEvent);
    wssStarted = true;
  }

  if (isMqttConnected && now - lastPing > 10000) {
    lastPing = now;
    uint8_t p[] = {0xC0, 0x00};
    webSocket.sendBIN(p, 2);
  }

  // Timeout jika server lambat atau tidak ada respons
  if ((currentState == PROCESSING || currentState == REGISTER_PROCESSING) &&
      (now - stateTimer > 5000)) {
    drawScreen("Error (Timeout)", "Server No Reply");
    digitalWrite(LED_RED, HIGH);
    beep(3, 100);

    delay(3000);
    digitalWrite(LED_RED, LOW);
    drawScreen("Keshir POS", "Tap kartu anda");
    currentState = IDLE;
  }

  // Deteksi Kartu RFID
  if (isMqttConnected &&
      (currentState == IDLE || currentState == REGISTER_MODE)) {
    if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
      String uid = "";
      for (byte i = 0; i < rfid.uid.size; i++) {
        uid += String(rfid.uid.uidByte[i] < 0x10 ? "0" : "");
        uid += String(rfid.uid.uidByte[i], HEX);
      }
      uid.toUpperCase();

      StaticJsonDocument<128> doc;
      doc["uid"] = uid;
      char jsonBuffer[128];
      serializeJson(doc, jsonBuffer);

      if (currentState == IDLE) {
        drawScreen("Memproses...", "Mohon tunggu");
        sendMqttPublish(mqtt_topic_pub_tap.c_str(), jsonBuffer);
        currentState = PROCESSING;
        stateTimer = millis();
      } else if (currentState == REGISTER_MODE) {
        drawScreen("Memproses...", "Mohon tunggu");
        doc["event"] = "card_scanned";
        serializeJson(doc, jsonBuffer);
        sendMqttPublish(mqtt_topic_pub_event.c_str(), jsonBuffer);
        currentState = REGISTER_PROCESSING;
        stateTimer = millis();
      }

      // Halt PICC
      rfid.PICC_HaltA();
      rfid.PCD_StopCrypto1();
    }
  }
}