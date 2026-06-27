document.addEventListener('DOMContentLoaded', () => {
    const btnCreateRoom = document.getElementById('btnCreateRoom');

    if (!btnCreateRoom) return;

    btnCreateRoom.addEventListener('click', () => {
        btnCreateRoom.classList.add('scale-95');

        setTimeout(() => {
            btnCreateRoom.classList.remove('scale-95');
        }, 150);
    });
});