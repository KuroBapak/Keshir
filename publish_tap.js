import mqtt from 'mqtt';

const client = mqtt.connect('wss://mqtt.kurobapak.site/mqtt', {
    username: 'ESP32',
    password: 'sudomoreno'
});

client.on('connect', () => {
    console.log('Publishing tap...');
    client.publish('keshir/attendance/front_door/up/tap', JSON.stringify({uid: "A1B2C3D4"}));
    setTimeout(() => process.exit(0), 2000);
});
