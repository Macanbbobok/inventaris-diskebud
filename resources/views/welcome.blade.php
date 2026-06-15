<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#143f39">

    <title>Inventaris Diskebud</title>

    @fonts

    <style>
        :root {
            --bg: #f5f7f4;
            --surface: #ffffff;
            --surface-muted: #eef2ef;
            --ink: #17211d;
            --muted: #66736c;
            --line: #dce4df;
            --forest: #143f39;
            --forest-soft: #dfece5;
            --amber: #bf7f1f;
            --brick: #b94734;
            --blue: #2b6f8f;
            --shadow: 0 22px 70px rgba(20, 63, 57, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: "Instrument Sans", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            display: block;
            max-width: 100%;
        }

        .page-shell {
            min-height: 100vh;
        }

        .container {
            width: min(1120px, calc(100% - 40px));
            margin: 0 auto;
        }

        .topbar {
            border-bottom: 1px solid rgba(20, 63, 57, 0.12);
            background: rgba(245, 247, 244, 0.94);
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(12px);
        }

        .topbar-inner {
            min-height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: var(--forest);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-weight: 800;
            flex: 0 0 auto;
        }

        .brand-text {
            display: grid;
            gap: 2px;
            min-width: 0;
        }

        .brand-title {
            font-size: 1rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 0.84rem;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
            color: var(--muted);
            font-size: 0.94rem;
            font-weight: 600;
        }

        .nav-links a:hover {
            color: var(--forest);
        }

        .button {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-radius: 8px;
            border: 1px solid var(--line);
            padding: 0 18px;
            font-weight: 800;
            transition: transform 160ms ease, border-color 160ms ease, background 160ms ease;
        }

        .button:hover {
            transform: translateY(-1px);
        }

        .button-primary {
            background: var(--forest);
            border-color: var(--forest);
            color: #ffffff;
        }

        .button-secondary {
            background: #ffffff;
            color: var(--forest);
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(360px, 0.82fr);
            gap: 52px;
            align-items: center;
            padding: 58px 0 42px;
        }

        .eyebrow {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(20, 63, 57, 0.16);
            border-radius: 999px;
            background: var(--forest-soft);
            color: var(--forest);
            padding: 8px 12px;
            font-size: 0.86rem;
            font-weight: 800;
        }

        .eyebrow-dot {
            width: 8px;
            height: 8px;
            border-radius: 99px;
            background: var(--amber);
        }

        h1 {
            max-width: 720px;
            margin: 22px 0 18px;
            font-size: 3.85rem;
            line-height: 1.03;
            font-weight: 900;
        }

        .lead {
            max-width: 650px;
            margin: 0;
            color: var(--muted);
            font-size: 1.12rem;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .quick-note {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
            color: var(--muted);
            font-size: 0.94rem;
            font-weight: 650;
        }

        .quick-note span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .quick-note span::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 99px;
            background: var(--blue);
        }

        .preview-panel {
            border: 1px solid rgba(20, 63, 57, 0.14);
            border-radius: 8px;
            background: var(--surface);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 18px 14px;
            border-bottom: 1px solid var(--line);
        }

        .preview-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 900;
        }

        .preview-status {
            color: var(--forest);
            background: var(--forest-soft);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 0.78rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            border-bottom: 1px solid var(--line);
        }

        .stat-item {
            padding: 18px;
            min-height: 112px;
            border-right: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .stat-item:nth-child(2n) {
            border-right: 0;
        }

        .stat-item:nth-last-child(-n + 2) {
            border-bottom: 0;
        }

        .stat-label {
            margin: 0 0 8px;
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 750;
        }

        .stat-value {
            margin: 0;
            font-size: 1.75rem;
            line-height: 1.1;
            font-weight: 900;
        }

        .stat-caption {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.82rem;
        }

        .condition-box {
            padding: 18px;
        }

        .section-kicker {
            margin: 0 0 13px;
            color: var(--muted);
            font-size: 0.84rem;
            font-weight: 850;
        }

        .condition-list {
            display: grid;
            gap: 12px;
        }

        .condition-row {
            display: grid;
            gap: 7px;
        }

        .condition-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            color: var(--muted);
            font-size: 0.83rem;
            font-weight: 750;
        }

        .condition-track {
            height: 9px;
            border-radius: 99px;
            background: var(--surface-muted);
            overflow: hidden;
        }

        .condition-fill {
            min-width: 4px;
            height: 100%;
            border-radius: inherit;
        }

        .condition-fill.is-good {
            background: var(--forest);
        }

        .condition-fill.is-warning {
            background: var(--amber);
        }

        .condition-fill.is-danger {
            background: var(--brick);
        }

        .asset-strip {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 0 18px 18px;
        }

        .asset-photo {
            margin: 0;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: var(--surface-muted);
            overflow: hidden;
            aspect-ratio: 4 / 3;
        }

        .asset-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .section {
            padding: 28px 0 70px;
        }

        .section-heading {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 28px;
            margin-bottom: 22px;
        }

        .section-heading h2 {
            margin: 0;
            font-size: 2rem;
            line-height: 1.15;
            font-weight: 900;
        }

        .section-heading p {
            max-width: 480px;
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .feature-card {
            min-height: 210px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
            padding: 20px;
        }

        .feature-number {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: #ffffff;
            background: var(--forest);
            font-size: 0.85rem;
            font-weight: 900;
        }

        .feature-card:nth-child(2) .feature-number {
            background: var(--blue);
        }

        .feature-card:nth-child(3) .feature-number {
            background: var(--amber);
        }

        .feature-card:nth-child(4) .feature-number {
            background: var(--brick);
        }

        .feature-card h3 {
            margin: 22px 0 10px;
            font-size: 1.1rem;
            font-weight: 900;
        }

        .feature-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.65;
        }

        .footer-band {
            border-top: 1px solid var(--line);
            padding: 24px 0 34px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        @media (max-width: 980px) {
            .hero {
                grid-template-columns: 1fr;
                gap: 34px;
            }

            .feature-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 760px) {
            .container {
                width: min(100% - 28px, 1120px);
            }

            .topbar-inner {
                min-height: auto;
                padding: 14px 0;
                align-items: flex-start;
            }

            .nav-links {
                display: none;
            }

            .brand-subtitle {
                white-space: normal;
            }

            h1 {
                font-size: 2.52rem;
            }

            .lead {
                font-size: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-item,
            .stat-item:nth-child(2n),
            .stat-item:nth-last-child(-n + 2) {
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }

            .stat-item:last-child {
                border-bottom: 0;
            }

            .section-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .footer-inner {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (max-width: 460px) {
            .brand-title {
                font-size: 0.94rem;
            }

            .button {
                width: 100%;
            }

            .hero {
                padding-top: 36px;
            }

            h1 {
                font-size: 2.18rem;
            }

            .asset-strip {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @php
        $stats = $stats ?? [
            ['label' => 'Total Aset', 'value' => '0', 'caption' => '0 aset aktif'],
            ['label' => 'Ruangan', 'value' => '0', 'caption' => '0 bidang terhubung'],
            ['label' => 'Laporan Kerusakan', 'value' => '0', 'caption' => 'Perlu tindak lanjut'],
            ['label' => 'Nilai Perolehan', 'value' => 'Rp 0', 'caption' => 'Akumulasi aset'],
        ];

        $conditionStats = $conditionStats ?? [
            ['label' => 'Baik', 'value' => 0, 'percent' => 0, 'class' => 'is-good'],
            ['label' => 'Rusak ringan', 'value' => 0, 'percent' => 0, 'class' => 'is-warning'],
            ['label' => 'Rusak berat', 'value' => 0, 'percent' => 0, 'class' => 'is-danger'],
        ];
    @endphp

    <div class="page-shell">
        <header class="topbar">
            <div class="container topbar-inner">
                <a class="brand" href="{{ url('/') }}" aria-label="Inventaris Diskebud">
                    <span class="brand-mark">ID</span>
                    <span class="brand-text">
                        <span class="brand-title">Inventaris Diskebud</span>
                        <span class="brand-subtitle">Sistem pencatatan aset internal</span>
                    </span>
                </a>

                <nav class="nav-links" aria-label="Navigasi utama">
                    <a href="#ringkasan">Ringkasan</a>
                    <a href="#fitur">Fitur</a>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </nav>
            </div>
        </header>

        <main>
            <section class="container hero" id="ringkasan">
                <div>
                    <span class="eyebrow">
                        <span class="eyebrow-dot" aria-hidden="true"></span>
                        Dinas Kebudayaan
                    </span>

                    <h1>Pusat kendali inventaris yang rapi dan mudah dipantau.</h1>

                    <p class="lead">
                        Kelola data barang, ruangan, QR aset, mutasi, pemeliharaan, dan laporan inventaris dari satu halaman kerja yang fokus.
                    </p>

                    <div class="hero-actions">
                        <a class="button button-primary" href="{{ url('/dashboard') }}">
                            {{ auth()->check() ? 'Buka Dashboard' : 'Masuk Dashboard' }}
                            <span aria-hidden="true">-&gt;</span>
                        </a>
                        <a class="button button-secondary" href="{{ route('laporan.barang.pdf') }}">
                            Laporan Barang
                            <span aria-hidden="true">PDF</span>
                        </a>
                    </div>

                    <div class="quick-note" aria-label="Cakupan sistem">
                        <span>KIB B</span>
                        <span>QR publik</span>
                        <span>Mutasi aset</span>
                    </div>
                </div>

                <aside class="preview-panel" aria-label="Ringkasan inventaris">
                    <div class="preview-header">
                        <p class="preview-title">Ringkasan Hari Ini</p>
                        <span class="preview-status">Data aktif</span>
                    </div>

                    <div class="stats-grid">
                        @foreach ($stats as $stat)
                            <div class="stat-item">
                                <p class="stat-label">{{ $stat['label'] }}</p>
                                <p class="stat-value">{{ $stat['value'] }}</p>
                                <p class="stat-caption">{{ $stat['caption'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="condition-box">
                        <p class="section-kicker">Kondisi Barang</p>
                        <div class="condition-list">
                            @foreach ($conditionStats as $condition)
                                <div class="condition-row">
                                    <div class="condition-meta">
                                        <span>{{ $condition['label'] }}</span>
                                        <span>{{ number_format($condition['value'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="condition-track" aria-hidden="true">
                                        <div class="condition-fill {{ $condition['class'] }}" style="width: {{ $condition['percent'] }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="asset-strip" aria-label="Contoh foto aset">
                        <figure class="asset-photo">
                            <img src="{{ asset('storage/barang/01KRTQZB188AMWA0VFB9TZFNEG.jpg') }}" alt="Foto aset laptop" onerror="this.parentElement.hidden = true">
                        </figure>
                        <figure class="asset-photo">
                            <img src="{{ asset('storage/barang/01KRZRW8TQ1NT6GG0ADQJ3P99H.jpg') }}" alt="Foto aset pendingin ruangan" onerror="this.parentElement.hidden = true">
                        </figure>
                    </div>
                </aside>
            </section>

            <section class="container section" id="fitur">
                <div class="section-heading">
                    <h2>Area kerja utama</h2>
                    <p>Halaman depan ini diarahkan ke workflow inventaris yang paling sering dipakai operator dan admin.</p>
                </div>

                <div class="feature-grid">
                    <article class="feature-card">
                        <span class="feature-number">01</span>
                        <h3>Data Barang</h3>
                        <p>Catatan aset, kode barang, kondisi, status, nilai perolehan, foto, dan detail KIB tersusun dalam satu data induk.</p>
                    </article>

                    <article class="feature-card">
                        <span class="feature-number">02</span>
                        <h3>Ruangan & Bidang</h3>
                        <p>Lokasi aset dapat dipetakan per ruangan dan bidang sehingga pencarian barang lebih cepat saat pemeriksaan.</p>
                    </article>

                    <article class="feature-card">
                        <span class="feature-number">03</span>
                        <h3>Kerusakan</h3>
                        <p>Laporan kerusakan masuk sebagai antrian kerja yang bisa diproses sampai selesai dan tercatat riwayatnya.</p>
                    </article>

                    <article class="feature-card">
                        <span class="feature-number">04</span>
                        <h3>Laporan</h3>
                        <p>Rekap barang dan inventaris periodik siap digunakan untuk kebutuhan administrasi, evaluasi, dan arsip.</p>
                    </article>
                </div>
            </section>
        </main>

        <footer class="footer-band">
            <div class="container footer-inner">
                <span>Inventaris Diskebud</span>
                <span>{{ now()->format('Y') }} - Sistem Inventaris Barang</span>
            </div>
        </footer>
    </div>
</body>
</html>
