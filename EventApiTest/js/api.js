const event = document.getElementById('event');
const url = 'http://127.0.0.1:8000/api/users';
fetch(url)
    .then(response => response.json())
    .then(data => {
        console.log(data);
        
    });