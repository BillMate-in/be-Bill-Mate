// Fungsi untuk mengirim data pesanan ke server Laravel
function sendDataToCalculated() {
    // 1. Ambil data dari inputan DOM di Dashboard (Temanmu yang handle bagian ini)
    const payload = {
        restaurantName: "Bakso Pak Kirno", // Contoh data dinamis 
        tableNumber: "04",
        items: [
            { name: "Bakso Super Pedas", price: 18000, qty: 1, orderedBy: "Andi" },
            { name: "Mie Ayam Jamur", price: 12000, qty: 1, orderedBy: "Budi" }
        ],
        members: ["Andi", "Budi"],
        additionalCosts: { taxPercent: 10, discount: 0, extraFees: 2500 }
    };

    // 2. Tembak API menggunakan HTTP Fetch
    fetch('http://localhost:8000/api/split-bill/calculate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        // 3. Terima hasil kalkulasi adil dari server kamu
        console.log("Hasil Kalkulasi Server:", data);
        
        // Di sini temanmu tinggal mengarahkan halaman ke nota.html 
        // sambil membawa data hasil kalkulasi ini untuk ditampilkan di struk[cite: 17, 27].
    })
    .catch(error => console.error('Error saat kalkulasi:', error));
}