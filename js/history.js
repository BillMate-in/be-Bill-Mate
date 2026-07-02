document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.flex.gap-sm button');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => {
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

/**
 * BILLMATE - HISTORY MANAGEMENT ENGINE (RICH JSON SCHEMA VERSION)
 * Pengembang: Erynd (Senior Backend Engineer & Tech Mentor)
 */

document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // 1. ANIMASI VISUAL TAB FILTER PILL
    // ==========================================
    const filterButtons = document.querySelectorAll('.flex.gap-sm button');

    filterButtons.forEach(button => {
        if (button.id === 'deleteAllBtn') return; // Lewati tombol hapus masal

        button.addEventListener('click', () => {
            filterButtons.forEach(btn => {
                if (btn.id === 'deleteAllBtn') return;
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

    // ==========================================
    // 2. INISIALISASI STATE & ELEMEN DOM UTAMA
    // ==========================================
    let historyData = []; // Menggunakan satu penamaan variabel 'historyData' secara konsisten
    const historyListContainer = document.getElementById('historyList');
    const searchInput = document.querySelector('input[placeholder="Cari room..."]');
    const deleteAllBtn = document.getElementById('deleteAllBtn');
    
    // Elemen pemicu filter waktu
    const allBtn = document.getElementById('allBtn');
    const weekBtn = document.getElementById('weekBtn');
    const monthBtn = document.getElementById('monthBtn');

    if (!historyListContainer) return;

    // ==========================================
    // 3. FUNGSI: MEMUAT DATA DARI STORAGE (LOAD)
    // ==========================================
    function loadHistoryFromStorage() {
        const rawHistory = localStorage.getItem('billHistory');
        historyData = rawHistory ? JSON.parse(rawHistory) : [];
        
        // Pengurutan Riwayat Berdasarkan Waktu (Timestamp):
        // Jika timestamp kosong, kita buat fallback cerdas menggunakan waktu saat ini (Date.now() / 1000)
        historyData.sort((a, b) => {
            const timeA = a.timestamp || Math.floor(Date.now() / 1000);
            const timeB = b.timestamp || Math.floor(Date.now() / 1000);
            return timeB - timeA;
        });
    }

    // ==========================================
    // 4. FUNGSI: MERENDER LIST RIWAYAT KE LAYAR
    // ==========================================
    function renderHistoryList(dataToRender = null) {
        const data = dataToRender || historyData;
        historyListContainer.innerHTML = ''; // Kosongkan elemen visual statis bawaan HTML

        // Tampilkan feedback visual jika data kosong
        if (data.length === 0) {
            historyListContainer.innerHTML = `
                <div class="text-center py-12 bg-surface-container-lowest rounded-2xl border border-surface-variant/30 w-full">
                    <span class="material-symbols-outlined text-secondary/40 text-5xl">folder_open</span>
                    <p class="text-secondary mt-2 text-sm font-medium">Belum ada catatan riwayat makan-makan.</p>
                </div>
            `;
            return;
        }

        // Loop dan rakit visualisasi kartu riwayat berdasarkan skema Rich JSON kalkulasi
        data.forEach(bill => {
            const card = document.createElement('div');
            card.className = "flex flex-col sm:flex-row justify-between items-start sm:items-center bg-surface-container-lowest p-md rounded-2xl border border-surface-variant/40 shadow-sm gap-md w-full";
            
            // EKSTRAKSI PROPERTI DARI RICH JSON SCHEMA:
            // - Nama Restoran langsung dibaca dari root objek
            const restaurantName = bill.restaurantName || 'Restoran Tanpa Nama';
            // - ID Transaksi langsung dibaca dari root objek
            const transactionId = bill.transactionId || '#BM-2026-0000';
            const date = bill.date || 'Unknown Date';
            
            // - Jumlah Anggota diekstraksi secara akurat dari panjang (.length) array membersBreakdown
            const membersCount = bill.membersBreakdown ? bill.membersBreakdown.length : 0;
            
            // - Nominal Grand Total diekstraksi dari objek properti bersarang bill.summary.grandTotal
            const grandTotal = bill.summary ? bill.summary.grandTotal : 0;

            card.innerHTML = `
                <div class="flex items-center gap-sm">
                    <span class="material-symbols-outlined text-primary-container bg-primary-fixed/20 p-sm rounded-xl">restaurant</span>
                    <div>
                        <h3 class="font-bold text-on-surface text-base">${restaurantName}</h3>
                        <div class="flex items-center gap-xs text-xs text-secondary mt-1">
                            <span class="font-semibold">${transactionId}</span>
                            <span>•</span>
                            <span>${date}</span>
                            <span>•</span>
                            <span>${membersCount} Anggota</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-md border-t sm:border-t-0 pt-xs sm:pt-0 border-surface-variant/20">
                    <span class="font-extrabold text-primary-container text-lg">Rp ${grandTotal.toLocaleString('id-ID')}</span>
                    <button class="btn-detail flex items-center gap-xs px-md py-sm bg-primary-container hover:bg-orange-400 text-white font-semibold text-xs rounded-xl shadow-sm transition-transform active:scale-95">
                        <span class="material-symbols-outlined text-sm">visibility</span>
                        <span>Lihat Detail</span>
                    </button>
                </div>
            `;

            // EVENT LISTENER: Tombol "Lihat Detail" di dalam kartu
            const detailBtn = card.querySelector('.btn-detail');
            detailBtn.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Setel objek utuh Rich JSON bill ini ke dalam key 'calculatedBill' agar langsung dicetak penuh oleh nota.html
                localStorage.setItem('calculatedBill', JSON.stringify(bill));
                
                // Alihkan ke nota.html
                window.location.href = 'nota.html';
            });

            historyListContainer.appendChild(card);
        });

        // Perbarui ringkasan pagination info di bagian bawah layar
        const pageInfo = document.getElementById('pageInfo');
        if (pageInfo) {
            pageInfo.textContent = `Riwayat: ${data.length} Transaksi`;
        }
    }

    // ==========================================
    // 5. INTERAKSI: REAL-TIME SEARCH (CARI ROOM)
    // ==========================================
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            
            // Melakukan pencarian dari struktur objek Rich JSON
            const filtered = historyData.filter(bill => {
                const restaurant = (bill.restaurantName || '').toLowerCase();
                const txId = (bill.transactionId || '').toLowerCase();
                return restaurant.includes(query) || txId.includes(query);
            });
            
            renderHistoryList(filtered);
        });
    }

    // ==========================================
    // 6. INTERAKSI: TOMBOL HAPUS SEMUA (DELETE ALL)
    // ==========================================
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', (e) => {
            e.preventDefault();

            if (historyData.length === 0) {
                alert('Riwayat transaksi Anda memang sudah kosong.');
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus seluruh daftar riwayat makan-makan? Tindakan ini bersifat permanen.')) {
                localStorage.removeItem('billHistory');
                historyData = [];
                renderHistoryList();
            }
        });
    }

    // ==========================================
    // 7. INTERAKSI: FILTER BERDASARKAN WAKTU (TIMESTAMP)
    // ==========================================
    const nowInSeconds = Math.floor(Date.now() / 1000);

    if (allBtn) {
        allBtn.addEventListener('click', () => renderHistoryList());
    }

    if (weekBtn) {
        weekBtn.addEventListener('click', () => {
            const sevenDaysAgo = nowInSeconds - (7 * 24 * 60 * 60);
            const filtered = historyData.filter(bill => {
                const billTime = bill.timestamp || nowInSeconds;
                return billTime >= sevenDaysAgo;
            });
            renderHistoryList(filtered);
        });
    }

    if (monthBtn) {
        monthBtn.addEventListener('click', () => {
            const thirtyDaysAgo = nowInSeconds - (30 * 24 * 60 * 60);
            const filtered = historyData.filter(bill => {
                const billTime = bill.timestamp || nowInSeconds;
                return billTime >= thirtyDaysAgo;
            });
            renderHistoryList(filtered);
        });
    }

    // ==========================================
    // 8. BOOTSTRAP: JALANKAN PROGRAM SAAT PAGE LOAD
    // ==========================================
    loadHistoryFromStorage();
    renderHistoryList();
});