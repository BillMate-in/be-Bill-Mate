/**
 * BILLMATE - ROOM INTERACTIVE ENGINE
 * Pengembang: Erynd (Senior Backend Engineer & Tech Mentor)
 * 
 * Semua fungsi interaktif disatukan di sini agar sinkronisasi data lokal (DOM)
 * dan kalkulasi matematika di sisi backend berjalan harmonis.
 */

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // 1. ANIMASI PIL FILTER (DENGAN SAFEGUARD)
    // ==========================================
    const filterButtons = document.querySelectorAll('.flex.gap-sm button');

    filterButtons.forEach(button => {
        // SAFEGUARD: Mencegah tombol fungsional utama agar tidak ikut terpengaruh oleh logika visual filter pill
        if (
            button.id === 'btnAddItem' || 
            button.id === 'btnJoinFake' || 
            button.id === 'lockRoomBtn'
        ) {
            return; 
        }

        button.addEventListener('click', () => {
            filterButtons.forEach(btn => {
                if (btn.id === 'btnAddItem' || btn.id === 'btnJoinFake' || btn.id === 'lockRoomBtn') return;
                btn.classList.remove('active-pill');
                btn.classList.add(
                    'bg-surface-container-lowest',
                    'border',
                    'border-surface-variant',
                    'text-secondary'
                );
            });

            button.classList.add('active-pill');
            button.classList.remove(
                'bg-surface-container-lowest',
                'border',
                'border-surface-variant',
                'text-secondary'
            );
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // 2. INISIALISASI ELEMEN UTAMA DASHBOARD
    // ==========================================
    const lockRoomBtn = document.getElementById('lockRoomBtn');
    const btnJoinFake = document.getElementById('btnJoinFake');
    const userInputSelect = document.getElementById('userInput');
    const btnAddItem = document.getElementById('btnAddItem');
    const foodInput = document.getElementById('foodInput');
    const priceInput = document.getElementById('priceInput');
    const qtyInput = document.getElementById('qtyInput');
    const itemList = document.getElementById('itemList');

    if (!lockRoomBtn) return;

    // Set nama restoran di judul utama room dari sessionStorage
    const roomTitle = document.getElementById('roomTitle');
    if (roomTitle) {
        const savedResto = sessionStorage.getItem('restaurantName') || 'restaurant(?)';
        roomTitle.textContent = `Room - ${savedResto}`;
    }

    // Default Host otomatis masuk ke dropdown saat halaman dimuat pertama kali
    if (userInputSelect && userInputSelect.options.length === 0) {
        const hostName = sessionStorage.getItem('hostName') || 'Host';
        const defaultOption = new Option(hostName, hostName);
        userInputSelect.add(defaultOption);
    }

    // ==========================================
    // 3. EVENT LISTENER: TAMBAH ANGGOTA BARU
    // ==========================================
    if (btnJoinFake && userInputSelect) {
        btnJoinFake.addEventListener('click', (e) => {
            e.preventDefault(); // Mencegah reload halaman

            const newMemberName = prompt('Masukkan nama anggota baru:');
            if (!newMemberName || newMemberName.trim() === '') return;
            const trimmedName = newMemberName.trim();

            // Validasi duplikasi: Mencegah nama yang sama dimasukkan dua kali
            const existingMembers = Array.from(userInputSelect.options).map(opt => opt.value.toLowerCase());
            if (existingMembers.includes(trimmedName.toLowerCase())) {
                alert('Nama anggota tersebut sudah ada di dalam room!');
                return;
            }

            // Tambahkan nama baru ke dropdown select
            const option = new Option(trimmedName, trimmedName);
            userInputSelect.add(option);
            userInputSelect.value = trimmedName; // Otomatis pilih nama baru tersebut
        });
    }

    // ==========================================
    // 4. FUNGSI: HITUNG ULANG RINGKASAN DI LAYAR (REAL-TIME PREVIEW)
    // ==========================================
    function recalculateLocalTotals() {
        let totalBaseCost = 0;

        // Kumpulkan semua kartu pesanan aktif dari DOM
        const itemCards = document.querySelectorAll('#itemList > .item-card');
        itemCards.forEach(card => {
            const price = parseFloat(card.getAttribute('data-price') || 0);
            const qty = parseInt(card.getAttribute('data-qty') || 1);
            totalBaseCost += (price * qty);
        });

        // Kumpulkan parameter biaya tambahan saat ini
        const taxPercent = parseFloat(document.getElementById('taxInput').value) || 0;
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const extraFees = parseFloat(document.getElementById('extraFee').value) || 0;

        // Logika kalkulasi matematis sederhana untuk visual preview di sisi client
        const taxAmount = totalBaseCost * (taxPercent / 100);
        const grandTotal = totalBaseCost + taxAmount - discount + extraFees;

        // Render hasil perhitungan ke elemen UI teks secara dinamis
        document.getElementById('subtotal').textContent = `Rp ${totalBaseCost.toLocaleString('id-ID')}`;
        document.getElementById('tax').textContent = `Rp ${taxAmount.toLocaleString('id-ID')}`;
        document.getElementById('fees').textContent = `Rp ${extraFees.toLocaleString('id-ID')}`;
        document.getElementById('grandTotal').textContent = `Rp ${Math.max(0, grandTotal).toLocaleString('id-ID')}`;
        // Tambahkan baris ini di baris terakhir di dalam fungsi recalculateLocalTotals Anda:
window.recalculateLocalTotals = recalculateLocalTotals;
    }

    // Pasang pendengar (listener) perubahan angka pada kolom input sebelah kanan agar langsung auto-recalculate
    ['taxInput', 'discountInput', 'extraFee'].forEach(id => {
        const inputElement = document.getElementById(id);
        if (inputElement) {
            inputElement.addEventListener('input', recalculateLocalTotals);
        }
    });

    // ==========================================
    // 5. EVENT LISTENER: TAMBAH PESANAN (ADD ITEM)
    // ==========================================
    if (btnAddItem && foodInput && priceInput && qtyInput && itemList) {
        btnAddItem.addEventListener('click', (e) => {
            e.preventDefault();

            const foodName = foodInput.value.trim();
            const foodPrice = parseFloat(priceInput.value) || 0;
            const foodQty = parseInt(qtyInput.value) || 1;
            const foodUser = userInputSelect.value;

            // Validasi input data pesanan
            if (!foodName) {
                alert('Mohon isi nama makanan atau minuman!');
                return;
            }
            if (foodPrice <= 0) {
                alert('Harga satuan harus berupa angka lebih besar dari Rp 0!');
                return;
            }
            if (foodQty <= 0) {
                alert('Kuantitas (Qty) minimal harus bernilai 1!');
                return;
            }
            if (!foodUser) {
                alert('Mohon pilih nama anggota pemesan terlebih dahulu!');
                return;
            }

            // Panggil fungsi pembantu createItemCardElement untuk merakit DOM elemen kartu pesanan
            const newItemCard = createItemCardElement(foodName, foodPrice, foodQty, foodUser);
            
            // Masukkan elemen baru ke dalam daftar pesanan aktif di layar
            itemList.appendChild(newItemCard);

            // Reset form input agar siap untuk input berikutnya
            foodInput.value = '';
            priceInput.value = '';
            qtyInput.value = '1';

            // Pemicu kalkulasi ulang agar subtotal dan grand total langsung sinkron di layar
            recalculateLocalTotals();

        });
    }

    // ==========================================
    // 6. EVENT LISTENER: KIRIM DATA KE BACKEND LARAVEL
    // ==========================================
    lockRoomBtn.addEventListener('click', async (e) => {
        e.preventDefault();

        // Ambil data restoran dan rekening dari sessionStorage hasil input createroom.html
        const restaurantName = sessionStorage.getItem('restaurantName') || 'Restoran Tanpa Nama';
        const hostName = sessionStorage.getItem('hostName') || 'Host';
        const tableNumber = sessionStorage.getItem('tableNumber') || 'Meja Umum';

        // Ambil seluruh nama anggota dari dropdown
        const members = Array.from(userInputSelect.options).map(option => option.value);

        if (members.length === 0) {
            alert('Gagal mengunci room. Minimal harus ada 1 anggota di dalam room!');
            return;
        }

        // Kumpulkan Daftar Pesanan Aktif (Items) dari DOM #itemList
        const items = [];
        const itemCards = document.querySelectorAll('#itemList > .item-card');

        itemCards.forEach(card => {
            const name = card.dataset.name || '';
            const price = parseFloat(card.dataset.price || 0);
            const qty = parseInt(card.dataset.qty || 1);
            const user = card.dataset.user || '';

            if (name) {
                items.push({ name, price, qty, user });
            }
        });

        if (items.length === 0) {
            alert('Gagal mengunci room. Harap masukkan minimal 1 menu pesanan!');
            return;
        }

        // Kumpulkan Komponen Biaya Tambahan (Additional Costs) dari Kolom Kanan
        const taxPercent = parseFloat(document.getElementById('taxInput').value) || 0;
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const extraFees = parseFloat(document.getElementById('extraFee').value) || 0;

        // Susun Payload JSON sesuai Kontrak Laravel Backend
        const payload = {
            restaurantName: restaurantName,
            tableNumber: tableNumber,
            hostName: hostName,
            members: members,
            items: items,
            additionalCosts: {
                taxPercent: taxPercent,
                discount: discount,
                extraFees: extraFees
            }
        };

        // Feedback Visual: Ubah tombol menjadi mode loading tanpa merusak class sekitarnya
        lockRoomBtn.disabled = true;
        lockRoomBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Memproses Perhitungan...</span>
        `;

        try {
            // Kirim data ke Laravel API
            const response = await fetch('http://127.0.0.1:8000/api/split-bill/calculate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // 1. Simpan hasil perhitungan aktif untuk dibaca halaman nota.html saat ini
                localStorage.setItem('calculatedBill', JSON.stringify(result.data));
                
                // 2. LOGIKA BARU: Ambil array riwayat lama atau buat array baru jika masih kosong
                const existingHistory = JSON.parse(localStorage.getItem('billHistory') || '[]');
                
                // Tambahkan data nota baru ke dalam array, sekaligus selipkan properti timestamp untuk sorting waktu
                const billWithTimestamp = { 
                    ...result.data, 
                    timestamp: Math.floor(Date.now() / 1000) 
                };
                
                existingHistory.push(billWithTimestamp);
                
                // Simpan kembali array kumpulan riwayat yang sudah terupdate ke localStorage
                localStorage.setItem('billHistory', JSON.stringify(existingHistory));
                
                // Redirect ke halaman nota
                window.location.replace('nota.html');
            } else {
                alert('Gagal menghitung: ' + (result.message || 'Terjadi kesalahan sistem.'));
                resetLockButton(lockRoomBtn);
            }
        } catch (error) {
            console.error('API Error:', error);
            alert('Gagal terhubung ke server Laravel backend. Pastikan server sudah dijalankan!');
            resetLockButton(lockRoomBtn);
        }
    });
});

// Fungsi pembantu untuk mengembalikan status tombol jika pengiriman gagal
function resetLockButton(button) {
    button.disabled = false;
    button.innerHTML = `
        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">lock</span>
        <span>Selesai & Kunci Room</span>
    `;
}

/**
 * FUNGSI PENOLONG (HELPER FUNCTION)
 * 
 * Membuat elemen kartu pesanan (DOM Element) baru secara dinamis.
 */
/**
 * FUNGSI PENOLONG (HELPER FUNCTION) - VERSI CRUD AKTIF
 * 
 * Membuat elemen kartu pesanan (DOM Element) baru secara dinamis lengkap dengan
 * tombol Edit (ikon pensil) dan Delete (ikon tempat sampah).
 */
function createItemCardElement(foodName, foodPrice, foodQty, foodUser) {
    const itemCard = document.createElement('div');
    
    // Memberikan class Tailwind agar tampilan kartu rapi
    itemCard.className = "item-card flex justify-between items-center bg-surface-container-low p-sm rounded-2xl shadow-sm border border-surface-variant/30";

    // Menanamkan data-attribute (sangat vital untuk dibaca oleh fungsi pengikis DOM)
    itemCard.setAttribute('data-name', foodName);
    itemCard.setAttribute('data-price', foodPrice);
    itemCard.setAttribute('data-qty', foodQty);
    itemCard.setAttribute('data-user', foodUser);
    
    // Menyusun struktur visual kartu pesanan lengkap dengan tombol Edit dan Delete
    itemCard.innerHTML = `
        <div class="flex items-center gap-sm">
            <span class="material-symbols-outlined text-primary-container bg-primary-fixed/20 p-sm rounded-xl">restaurant</span>
            <div>
                <!-- Menggunakan penanda class khusus agar teks mudah dimanipulasi saat proses EDIT -->
                <h4 class="font-bold text-on-surface text-sm item-name-text">${foodName}</h4>
                <p class="text-xs text-secondary font-medium item-details-text">${foodUser} • Rp ${parseFloat(foodPrice).toLocaleString('id-ID')}</p>
            </div>
        </div>
        <div class="flex items-center gap-md pr-sm">
            <span class="font-extrabold text-primary-container bg-primary-fixed/30 px-md py-sm rounded-xl text-xs item-qty-text">x${foodQty}</span>
            
            <!-- Tombol Aksi Kontrol (Edit & Delete) -->
            <div class="flex items-center gap-xs">
                <button class="btn-edit text-secondary hover:text-primary-container p-xs transition-colors" title="Edit Pesanan">
                    <span class="material-symbols-outlined text-base">edit</span>
                </button>
                <button class="btn-delete text-error hover:text-red-700 p-xs transition-colors" title="Hapus Pesanan">
                    <span class="material-symbols-outlined text-base">delete</span>
                </button>
            </div>
        </div>
    `;

    // ==========================================
    // AKSI 1: LOGIKA TOMBOL HAPUS (DELETE)
    // ==========================================
    const deleteBtn = itemCard.querySelector('.btn-delete');
    deleteBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const currentName = itemCard.getAttribute('data-name');
        
        if (confirm(`Apakah Anda yakin ingin menghapus pesanan "${currentName}"?`)) {
            // Hapus kartu elemen dari struktur HTML halaman
            itemCard.remove();

            // Pemicu hitung ulang subtotal dan grand total instan di panel kanan jika fungsi global tersedia
            if (typeof window.recalculateLocalTotals === 'function') {
                window.recalculateLocalTotals();
            }
        }
    });

    // ==========================================
    // AKSI 2: LOGIKA TOMBOL EDIT (UPDATE)
    // ==========================================
    const editBtn = itemCard.querySelector('.btn-edit');
    editBtn.addEventListener('click', (e) => {
        e.preventDefault();

        // Ambil nilai data-attribute yang saat ini aktif tersimpan di kartu
        const currentName = itemCard.getAttribute('data-name');
        const currentPrice = itemCard.getAttribute('data-price');
        const currentQty = itemCard.getAttribute('data-qty');
        const currentUser = itemCard.getAttribute('data-user');

        // 1. Ambil input pembaruan menggunakan popup prompt interaktif
        const newName = prompt('Ubah nama makanan/minuman:', currentName);
        if (newName === null) return; // Batalkan jika menekan tombol Cancel
        if (newName.trim() === '') {
            alert('Nama makanan/minuman tidak boleh kosong!');
            return;
        }

        const newPriceRaw = prompt('Ubah harga satuan (Rp):', currentPrice);
        if (newPriceRaw === null) return;
        const newPrice = parseFloat(newPriceRaw) || 0;
        if (newPrice <= 0) {
            alert('Harga satuan harus berupa angka lebih besar dari Rp 0!');
            return;
        }

        const newQtyRaw = prompt('Ubah jumlah pesanan (Qty):', currentQty);
        if (newQtyRaw === null) return;
        const newQty = parseInt(newQtyRaw) || 1;
        if (newQty <= 0) {
            alert('Jumlah pesanan minimal bernilai 1!');
            return;
        }

        // 2. Perbarui data-attribute pada elemen kartu pembungkus (sangat krusial untuk scraper)
        itemCard.setAttribute('data-name', newName.trim());
        itemCard.setAttribute('data-price', newPrice);
        itemCard.setAttribute('data-qty', newQty);

        // 3. Perbarui visual teks DOM di dalam kartu agar sinkron dengan data baru
        itemCard.querySelector('.item-name-text').textContent = newName.trim();
        itemCard.querySelector('.item-details-text').textContent = `${currentUser} • Rp ${newPrice.toLocaleString('id-ID')}`;
        itemCard.querySelector('.item-qty-text').textContent = `x${newQty}`;

        // 4. Pemicu hitung ulang subtotal dan grand total instan di panel kanan jika fungsi global tersedia
        if (typeof window.recalculateLocalTotals === 'function') {
            window.recalculateLocalTotals();
        }
    });

    return itemCard;
}