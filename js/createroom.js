document.addEventListener('DOMContentLoaded', () => {
    const inputFields = document.querySelectorAll('input');
    inputFields.forEach(input => {
        input.addEventListener('focus', () => {
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const btnCreateRoom = document.getElementById('btnCreateRoom');

    if (btnCreateRoom) {
        btnCreateRoom.addEventListener('click', () => {
            window.location.href = 'room.html'; 
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const btnCreateRoom = document.getElementById('btnCreateRoom');
    
    const hostInput = document.getElementById('host-name');
    const restaurantInput = document.getElementById('restaurant');
    const rekeningInput = document.getElementById('rekening');

    if (btnCreateRoom) {
        btnCreateRoom.addEventListener('click', () => {
            if (!hostInput || hostInput.value.trim() === "") {
                alert("Peringatan: Host Name harus diisi!");
                hostInput.focus();
                return;
            }

            if (!restaurantInput || restaurantInput.value.trim() === "") {
                alert("Peringatan: Restaurant Name harus diisi!");
                restaurantInput.focus();
                return; 
            }

            if (!rekeningInput || rekeningInput.value.trim() === "") {
                alert("Peringatan: Nomor Rekening harus diisi!");
                rekeningInput.focus();
                return; 
            }
            window.location.href = 'room.html';
        });
    }
});