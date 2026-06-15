<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $barang->nama_barang }} - Detail Barang</title>

    @php
        $formatLabel = fn (?string $value): string => filled($value)
            ? str($value)->replace('_', ' ')->title()
            : '-';

        $conditionClass = match ($barang->kondisi) {
            'baik' => 'is-good',
            'rusak_ringan' => 'is-warning',
            'rusak_berat' => 'is-danger',
            default => 'is-muted',
        };

        $statusClass = match ($barang->status) {
            'aktif' => 'is-good',
            'dipinjam' => 'is-warning',
            'dihapus' => 'is-danger',
            default => 'is-muted',
        };

        $hargaPerolehan = filled($barang->harga_perolehan)
            ? 'Rp ' . number_format((float) $barang->harga_perolehan, 0, ',', '.')
            : '-';

        $hasKibB = filled($barang->detailKibB);
        $templateName = $hasKibB ? 'Template Barang + KIB B' : 'Template Barang';
        $templateDescription = $hasKibB
            ? 'Informasi inventaris resmi yang memuat data barang utama beserta detail KIB B Peralatan dan Mesin.'
            : 'Informasi inventaris resmi yang memuat data barang utama tanpa detail KIB B.';
    @endphp

    <style>
        :root {
            --ink: #10241d;
            --muted: #65746d;
            --line: #dfe7e1;
            --surface: #ffffff;
            --surface-soft: #f6f8f4;
            --green: #15513d;
            --green-dark: #0b2f24;
            --gold: #d3a12f;
            --gold-soft: #fff3bf;
            --blue: #2563eb;
            --red: #b42318;
            --shadow: 0 22px 60px rgba(16, 36, 29, 0.13);
        }

        * {
            box-sizing: border-box;
            letter-spacing: 0;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--ink);
            background:
                linear-gradient(135deg, rgba(246, 248, 244, 0.96), rgba(255, 253, 242, 0.95)),
                repeating-linear-gradient(90deg, rgba(21, 81, 61, 0.07) 0 1px, transparent 1px 76px),
                repeating-linear-gradient(0deg, rgba(211, 161, 47, 0.06) 0 1px, transparent 1px 76px);
            padding: 32px;
        }

        .page {
            width: min(1040px, 100%);
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 16px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: var(--green);
            color: #ffffff;
            display: grid;
            place-items: center;
            font-weight: 900;
        }

        .brand-title,
        .brand-subtitle {
            display: block;
        }

        .brand-title {
            font-weight: 900;
            line-height: 1.1;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 0.88rem;
            margin-top: 3px;
        }

        .scan-label {
            border: 1px solid rgba(21, 81, 61, 0.16);
            border-radius: 8px;
            color: var(--green);
            background: rgba(255, 255, 255, 0.68);
            padding: 10px 12px;
            font-size: 0.82rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .panel {
            overflow: hidden;
            border: 1px solid rgba(21, 81, 61, 0.12);
            border-radius: 8px;
            background: var(--surface);
            box-shadow: var(--shadow);
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 28px;
            padding: 30px;
            background:
                linear-gradient(135deg, rgba(11, 47, 36, 0.98), rgba(21, 81, 61, 0.94)),
                repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.05) 0 1px, transparent 1px 66px);
            color: #ffffff;
        }

        .eyebrow {
            display: inline-flex;
            width: fit-content;
            border: 1px solid rgba(244, 196, 48, 0.34);
            border-radius: 8px;
            background: rgba(244, 196, 48, 0.13);
            color: var(--gold-soft);
            padding: 8px 10px;
            font-size: 0.82rem;
            font-weight: 850;
        }

        h1 {
            margin: 16px 0 14px;
            font-size: clamp(2rem, 4vw, 3.2rem);
            line-height: 1.03;
            font-weight: 950;
        }

        .hero-copy {
            max-width: 620px;
            color: rgba(255, 255, 255, 0.78);
            font-size: 1rem;
            line-height: 1.7;
            margin: 0;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .badge {
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            padding: 9px 11px;
            font-size: 0.82rem;
            font-weight: 850;
        }

        .badge.is-good {
            border-color: rgba(112, 207, 151, 0.42);
            background: rgba(34, 197, 94, 0.16);
        }

        .badge.is-warning {
            border-color: rgba(244, 196, 48, 0.5);
            background: rgba(244, 196, 48, 0.15);
        }

        .badge.is-danger {
            border-color: rgba(248, 113, 113, 0.48);
            background: rgba(180, 35, 24, 0.18);
        }

        .asset-media {
            min-height: 220px;
            border: 1px solid rgba(244, 196, 48, 0.24);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .asset-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .asset-placeholder {
            width: 84px;
            height: 84px;
            border-radius: 8px;
            border: 1px solid rgba(244, 196, 48, 0.36);
            background: rgba(244, 196, 48, 0.13);
            color: var(--gold-soft);
            display: grid;
            place-items: center;
            font-size: 2rem;
            font-weight: 950;
        }

        .content {
            padding: 30px;
        }

        .section + .section {
            border-top: 1px solid var(--line);
            margin-top: 28px;
            padding-top: 28px;
        }

        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 18px;
        }

        .section-title h2 {
            margin: 0;
            font-size: 1.15rem;
            line-height: 1.2;
        }

        .section-note {
            color: var(--muted);
            font-size: 0.86rem;
            font-weight: 700;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 20px;
        }

        .info {
            min-width: 0;
            border-bottom: 1px solid rgba(21, 81, 61, 0.1);
            padding: 0 0 12px;
        }

        .info-label {
            color: var(--muted);
            display: block;
            font-size: 0.78rem;
            font-weight: 800;
            line-height: 1.35;
            margin-bottom: 5px;
        }

        .info-value {
            display: block;
            color: var(--ink);
            font-size: 1rem;
            font-weight: 850;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .status-chip {
            display: inline-flex;
            width: fit-content;
            border-radius: 8px;
            padding: 6px 9px;
            font-size: 0.86rem;
            font-weight: 900;
        }

        .status-chip.is-good {
            background: #dcfce7;
            color: #166534;
        }

        .status-chip.is-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-chip.is-danger {
            background: #fee2e2;
            color: var(--red);
        }

        .status-chip.is-muted {
            background: #eef2f7;
            color: #475569;
        }

        .template-note {
            border: 1px solid rgba(21, 81, 61, 0.12);
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(246, 248, 244, 0.98), rgba(255, 253, 242, 0.96));
            color: var(--muted);
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1.55;
            margin-top: 22px;
            padding: 14px 16px;
        }

        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-top: 1px solid var(--line);
            background: var(--surface-soft);
            padding: 18px 30px;
            color: var(--muted);
            font-size: 0.86rem;
            font-weight: 700;
        }

        .home-link {
            border: 1px solid rgba(21, 81, 61, 0.16);
            border-radius: 8px;
            color: var(--green);
            background: #ffffff;
            padding: 10px 12px;
            text-decoration: none;
            font-weight: 900;
        }

        .home-link:hover {
            border-color: var(--gold);
            color: var(--green-dark);
        }

        @media (max-width: 820px) {
            body {
                padding: 18px;
            }

            .topbar,
            .footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero {
                grid-template-columns: 1fr;
                padding: 24px;
            }

            .asset-media {
                min-height: 210px;
            }

            .content {
                padding: 24px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="topbar">
            <div class="brand" aria-label="Inventaris Diskebud">
                <span class="brand-mark">ID</span>
                <span>
                    <span class="brand-title">Inventaris Diskebud</span>
                    <span class="brand-subtitle">Detail aset hasil pemindaian QR</span>
                </span>
            </div>

            <span class="scan-label">{{ $templateName }}</span>
        </div>

        <article class="panel">
            <section class="hero">
                <div>
                    <span class="eyebrow">Kode Barang: {{ $barang->kode_barang }}</span>
                    <h1>{{ $barang->nama_barang }}</h1>
                    <p class="hero-copy">{{ $templateDescription }}</p>

                    <div class="badges" aria-label="Status ringkas barang">
                        <span class="badge {{ $conditionClass }}">Kondisi: {{ $formatLabel($barang->kondisi) }}</span>
                        <span class="badge {{ $statusClass }}">Status: {{ $formatLabel($barang->status) }}</span>
                        @if($hasKibB)
                            <span class="badge">KIB B</span>
                        @else
                            <span class="badge">Barang Umum</span>
                        @endif
                    </div>
                </div>

                <div class="asset-media" aria-label="Foto barang">
                    @if($barang->foto)
                        <img src="{{ asset('storage/' . $barang->foto) }}" alt="Foto {{ $barang->nama_barang }}">
                    @else
                        <span class="asset-placeholder">{{ str($barang->nama_barang)->substr(0, 1)->upper() }}</span>
                    @endif
                </div>
            </section>

            <div class="content">
                <section class="section" aria-labelledby="data-utama">
                    <div class="section-title">
                        <h2 id="data-utama">Data Utama Barang</h2>
                        <span class="section-note">{{ $hasKibB ? 'Ringkasan inventaris' : 'Template barang' }}</span>
                    </div>

                    <div class="info-grid">
                        <div class="info">
                            <span class="info-label">Kode Barang</span>
                            <span class="info-value">{{ $barang->kode_barang }}</span>
                        </div>
                        <div class="info">
                            <span class="info-label">Ruangan</span>
                            <span class="info-value">{{ $barang->ruangan?->nama_ruangan ?? '-' }}</span>
                        </div>
                        <div class="info">
                            <span class="info-label">Bidang Penanggung Jawab</span>
                            <span class="info-value">{{ $barang->bidang?->nama_bidang ?? '-' }}</span>
                        </div>
                        <div class="info">
                            <span class="info-label">Kondisi</span>
                            <span class="status-chip {{ $conditionClass }}">{{ $formatLabel($barang->kondisi) }}</span>
                        </div>
                        <div class="info">
                            <span class="info-label">Status</span>
                            <span class="status-chip {{ $statusClass }}">{{ $formatLabel($barang->status) }}</span>
                        </div>
                        <div class="info">
                            <span class="info-label">Tanggal Perolehan</span>
                            <span class="info-value">{{ $barang->tanggal_perolehan?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <div class="info">
                            <span class="info-label">Tahun Perolehan</span>
                            <span class="info-value">{{ $barang->tahun_perolehan ?? '-' }}</span>
                        </div>
                        <div class="info">
                            <span class="info-label">Nilai Perolehan</span>
                            <span class="info-value">{{ $hargaPerolehan }}</span>
                        </div>
                    </div>

                    @unless($hasKibB)
                        <div class="template-note">
                            Barang ini ditampilkan menggunakan template data barang umum. Detail KIB B tidak ditampilkan karena barang belum memiliki data KIB B Peralatan dan Mesin.
                        </div>
                    @endunless
                </section>

                @if($hasKibB)
                    <section class="section" aria-labelledby="detail-kib">
                        <div class="section-title">
                            <h2 id="detail-kib">Detail KIB B</h2>
                            <span class="section-note">Peralatan dan Mesin</span>
                        </div>

                        <div class="info-grid">
                            <div class="info">
                                <span class="info-label">Merk / Type</span>
                                <span class="info-value">{{ $barang->detailKibB->merk_type ?? '-' }}</span>
                            </div>
                            <div class="info">
                                <span class="info-label">Ukuran</span>
                                <span class="info-value">{{ $barang->detailKibB->ukuran ?? '-' }}</span>
                            </div>
                            <div class="info">
                                <span class="info-label">Bahan</span>
                                <span class="info-value">{{ $barang->detailKibB->bahan ?? '-' }}</span>
                            </div>
                            <div class="info">
                                <span class="info-label">No Seri</span>
                                <span class="info-value">{{ $barang->detailKibB->no_seri ?? '-' }}</span>
                            </div>
                            <div class="info">
                                <span class="info-label">Spesifikasi</span>
                                <span class="info-value">{{ $barang->detailKibB->spesifikasi ?? '-' }}</span>
                            </div>
                        </div>
                    </section>
                @endif
            </div>

            <footer class="footer">
                <span>Informasi ditampilkan dari database inventaris Dinas Kebudayaan Provinsi Riau.</span>
                <a class="home-link" href="{{ url('/') }}">Beranda</a>
            </footer>
        </article>
    </main>
</body>
</html>
