document.addEventListener('DOMContentLoaded', () => {
    // 1. Inisialisasi tombol pemicu dan elemen-elemen input form
    // Catatan untuk mahasiswa: Pastikan elemen input pada createroom.html Anda memiliki id="hostName", id="restaurantName", dan id="bankAccount"
    const btnCreateRoom = document.getElementById('btnCreateRoom');
    const hostNameInput = document.getElementById('hostName');
    const restaurantNameInput = document.getElementById('restaurantName');
    const bankAccountInput = document.getElementById('bankName');
    const rekeningInput = document.getElementById('rekening');

    if (!btnCreateRoom) return;

    btnCreateRoom.addEventListener('click', (event) => {
        // Mencegah reload halaman bawaan browser jika tombol berada di dalam tag <form>
        event.preventDefault();

        // Efek animasi ketukan (feedback visual ke pengguna)
        btnCreateRoom.classList.add('scale-95');
        setTimeout(() => {
            btnCreateRoom.classList.remove('scale-95');
        }, 150);

        // 2. Validasi Input: Mengambil nilai dan membersihkan spasi kosong di awal/akhir (.trim())
        const hostName = hostNameInput ? hostNameInput.value.trim() : '';
        const restaurantName = restaurantNameInput ? restaurantNameInput.value.trim() : '';
        const bankName = bankAccountInput ? bankAccountInput.value.trim() : '';
        const rekening = rekeningInput ? rekeningInput.value.trim() : '';

        // Jika ada salah satu kolom wajib yang kosong, tampilkan peringatan dan batalkan aksi
        if (!hostName || !restaurantName || !bankName || !rekening) {
            alert('Gagal membuat ruangan. Mohon isi Nama Host, Nama Restoran, dan No. Rekening / No. E-Wallet terlebih dahulu!');
            return; 
        }

        // 3. Penyimpanan Sesi: Simpan data terverifikasi ke dalam sessionStorage browser
        // Data ini aman tersimpan selama tab browser aktif dan akan dibaca oleh room.html nantinya
        sessionStorage.setItem('hostName', hostName);
        sessionStorage.setItem('restaurantName', restaurantName);
        sessionStorage.setItem('bankName', bankName);
        sessionStorage.setItem('rekening', rekening)

        // 4. Pengalihan Halaman: Pindahkan pengguna secara otomatis ke dashboard room
        window.location.href = 'room.html';
    });
});
