<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BillMate Backend API Platform - Solusi Split Bill Adil & Terintegrasi">
    <title>BillMate Backend API Platform</title>
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-secondary: #111827;
            --bg-card: #1f2937;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --accent: #10b981;
            --accent-hover: #34d399;
            --accent-glow: rgba(16, 185, 129, 0.15);
            --border-color: #374151;
            --success: #10b981;
            --purple: #8b5cf6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-x: hidden;
            position: relative;
        }

        /* Background Glows */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0) 70%);
            top: -100px;
            right: -100px;
            z-index: -1;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.08) 0%, rgba(139, 92, 246, 0) 70%);
            bottom: -150px;
            left: -150px;
            z-index: -1;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 3rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .brand span {
            color: var(--accent);
        }

        .status-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--accent-glow);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--accent);
            padding: 0.4rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent);
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px var(--accent);
            animation: pulse 2s infinite;
        }

        /* Hero Section */
        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 4rem;
            align-items: center;
            margin-bottom: 4rem;
        }

        @media (max-width: 968px) {
            .hero {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2.5rem;
            }
            .hero-actions {
                justify-content: center;
            }
        }

        .hero-content h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 1.5rem;
        }

        .hero-content h1 span {
            background: linear-gradient(135deg, var(--accent) 0%, #6ee7b7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            color: var(--text-secondary);
            font-size: 1.125rem;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            max-width: 600px;
        }

        @media (max-width: 968px) {
            .hero-content p {
                margin-left: auto;
                margin-right: auto;
            }
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.95rem 1.75rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--accent);
            color: #0b0f19;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
        }

        .btn-primary:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: var(--text-secondary);
            transform: translateY(-2px);
        }

        /* Features/API Info */
        .api-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        @media (max-width: 768px) {
            .api-info {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            border-color: var(--accent);
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background-color: var(--accent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card:hover::before {
            opacity: 1;
        }

        .card.card-purple::before {
            background-color: var(--purple);
        }
        .card.card-purple:hover {
            border-color: var(--purple);
        }

        .card-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            font-size: 1.25rem;
        }

        .card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .card p {
            color: var(--text-secondary);
            font-size: 0.925rem;
            line-height: 1.5;
        }

        .card .endpoint {
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.8rem;
            background-color: rgba(0, 0, 0, 0.3);
            padding: 0.4rem 0.6rem;
            border-radius: 0.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #6ee7b7;
        }

        .card.card-purple .endpoint {
            color: #c084fc;
        }

        .endpoint span {
            background-color: var(--accent);
            color: #0b0f19;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .card.card-purple .endpoint span {
            background-color: var(--purple);
            color: white;
        }

        /* Mockup Visual */
        .mockup {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        .mockup-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .dot-red { background-color: #ef4444; }
        .dot-yellow { background-color: #f59e0b; }
        .dot-green { background-color: #10b981; }

        .mockup-body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem;
            color: #a7f3d0;
            line-height: 1.5;
            background-color: #05070c;
            padding: 1.25rem;
            border-radius: 0.75rem;
            overflow-x: auto;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 2rem 0;
            border-top: 1px solid var(--border-color);
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-top: auto;
        }

        /* Keyframes */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: .5;
                transform: scale(1.15);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <a href="#" class="brand">
                💸 Bill<span>Mate</span>
            </a>
            <div class="status-badge">
                <span class="status-dot"></span>
                API Server Online
            </div>
        </header>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>Platform Backend API <span>BillMate</span></h1>
                <p>
                    Solusi handal untuk penghitungan dan pembagian tagihan secara adil (Split Bill). 
                    Dilengkapi dengan penyimpanan database relasional yang dinormalisasi untuk menjaga integritas data riwayat transaksi Anda.
                </p>
                <div class="hero-actions">
                    <a href="/api/docs" class="btn btn-primary">
                        📖 Buka Dokumentasi Swagger UI
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="mockup">
                <div class="mockup-header">
                    <div class="dot dot-red"></div>
                    <div class="dot dot-yellow"></div>
                    <div class="dot dot-green"></div>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); margin-left: 0.5rem; font-family: monospace;">BillMate-calc.json</span>
                </div>
                <pre class="mockup-body">
{
  "success": true,
  "restaurantName": "Padang Sederhana",
  "summary": {
    "totalBaseCost": 77000,
    "grandTotal": 81700
  },
  "members": [
    "Ahmad", "Budi", "Cici"
  ]
}</pre>
            </div>
        </section>

        <!-- API Info Grid -->
        <section class="api-info">
            <div class="card">
                <div class="card-icon">🚀</div>
                <h3>Kalkulasi Split Bill</h3>
                <p>Hitung tagihan secara proporsional berdasarkan item pesanan masing-masing anggota termasuk pembagian pajak dan diskon secara adil.</p>
                <div class="endpoint">
                    <span>POST</span> /api/split-bill/calculate
                </div>
            </div>

            <div class="card">
                <div class="card-icon">🔒</div>
                <h3>Arsip Transaksi</h3>
                <p>Mengunci sesi room tagihan dan membuat payload arsip transaksi lengkap beserta generator kode hash arsip unik.</p>
                <div class="endpoint">
                    <span>POST</span> /api/split-bill/archive
                </div>
            </div>

            <div class="card card-purple">
                <div class="card-icon">📁</div>
                <h3>Swagger UI Docs</h3>
                <p>Dokumentasi interaktif OpenAPI 3.0 yang memudahkan frontend developer memahami schema data request, parameter, dan testing response.</p>
                <div class="endpoint">
                    <span>GET</span> /api/docs
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 BillMate API Platform. Dibuat dengan ❤️ untuk Frontend & Mobile Developer.</p>
    </footer>
</body>
</html>
