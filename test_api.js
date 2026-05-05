import axios from 'axios';

(async () => {
    try {
        const apiUrl = 'http://127.0.0.1:8000/api/attendance/tap';
        const response = await axios.post(apiUrl, {
            uid: "A1B2C3D4",
            device_id: "front_door"
        });
        console.log('Success:', response.data);
    } catch (e) {
        console.log('Error:', e.response ? e.response.data : e.message);
    }
})();
