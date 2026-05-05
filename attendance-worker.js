import mqtt from 'mqtt';
import axios from 'axios';

const brokerUrl = 'wss://mqtt.kurobapak.site/mqtt';
const username = 'ESP32';
const password = 'sudomoreno';

console.log(`Connecting to ${brokerUrl}...`);

const client = mqtt.connect(brokerUrl, {
    username,
    password,
    clientId: 'keshir_backend_' + Math.random().toString(16).substr(2, 8)
});

client.on('connect', () => {
    console.log('Connected via WSS!');
    client.subscribe('keshir/attendance/+/up/tap', (err) => {
        if (!err) {
            console.log('Subscribed to keshir/attendance/+/up/tap');
        } else {
            console.error('Subscribe error:', err);
        }
    });
});

client.on('message', async (topic, message) => {
    console.log(`Received message on ${topic}: ${message.toString()}`);
    
    try {
        const parts = topic.split('/');
        const deviceId = parts[2];
        const payload = JSON.parse(message.toString());
        
        if (!payload.uid) return;
        
        // Use the local Laravel API or localhost
        const apiUrl = 'http://127.0.0.1:8000/api/attendance/tap';
        
        const response = await axios.post(apiUrl, {
            uid: payload.uid,
            device_id: deviceId
        });
        
        const resData = response.data;
        const responseTopic = `keshir/attendance/${deviceId}/down/response`;
        
        client.publish(responseTopic, JSON.stringify(resData));
        console.log(`Published response to ${responseTopic}:`, JSON.stringify(resData));
        
    } catch (e) {
        console.error('Error processing tap:', e.message);
    }
});

client.on('error', (err) => {
    console.error('MQTT error:', err);
});
