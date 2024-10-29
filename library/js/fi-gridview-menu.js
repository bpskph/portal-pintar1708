// Menampilkan menu ketika pilih tombol export pada gridview
const button = document.getElementById('w2-button');
const dropdown = document.getElementById('w3');
const buttonpilih = document.getElementById('w0-cols');
const dropdownpilih = document.getElementById('w0-cols-list');

button.addEventListener('click', () => {
    dropdown.classList.toggle('show');
});
buttonpilih.addEventListener('click', () => {
    dropdownpilih.classList.toggle('show');
});

document.addEventListener('click', (event) => {
    if (!event.target.matches('#w2-button, #w3')) {
        dropdown.classList.remove('show');
    }
    if (!event.target.matches('#w0-cols, #w0-cols-list')) {
        dropdownpilih.classList.remove('show');
    }
});