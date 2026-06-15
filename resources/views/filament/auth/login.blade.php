<main class="inventory-login">
    <style>
        .inventory-login {
            --green-950: #0b2f24;
            --green-900: #103d2f;
            --green-800: #15513d;
            --green-700: #1d6b4d;
            --gold-600: #b8871e;
            --gold-500: #d3a12f;
            --yellow-400: #f4c430;
            --yellow-100: #fff3bf;
            --cream: #fffdf2;
            min-height: 100svh;
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(135deg, rgba(236, 244, 232, 0.96), rgba(255, 253, 242, 0.98)),
                repeating-linear-gradient(90deg, rgba(21, 81, 61, 0.07) 0 1px, transparent 1px 78px),
                repeating-linear-gradient(0deg, rgba(211, 161, 47, 0.06) 0 1px, transparent 1px 78px);
            color: var(--green-950);
            display: grid;
            place-items: center;
            padding: 28px;
        }

        .inventory-login::before {
            content: "";
            position: absolute;
            inset: -20%;
            pointer-events: none;
            background:
                linear-gradient(115deg, transparent 0 50%, rgba(21, 81, 61, 0.09) 50% 50.35%, transparent 50.35%),
                linear-gradient(115deg, transparent 0 63%, rgba(211, 161, 47, 0.1) 63% 63.28%, transparent 63.28%),
                linear-gradient(25deg, transparent 0 44%, rgba(244, 196, 48, 0.12) 44% 44.25%, transparent 44.25%);
        }

        .inventory-login::after {
            content: "";
            position: absolute;
            right: max(20px, calc((100vw - 1180px) / 2 - 90px));
            top: 9%;
            width: min(420px, 32vw);
            height: 72%;
            border: 1px solid rgba(21, 81, 61, 0.1);
            border-radius: 8px;
            pointer-events: none;
            background:
                linear-gradient(90deg, rgba(21, 81, 61, 0.055) 0 1px, transparent 1px 100%),
                linear-gradient(0deg, rgba(211, 161, 47, 0.055) 0 1px, transparent 1px 100%),
                linear-gradient(135deg, rgba(255, 243, 191, 0.28), rgba(21, 81, 61, 0.035));
            background-size: 54px 54px, 54px 54px, auto;
            box-shadow:
                -70px 90px 0 -1px rgba(21, 81, 61, 0.035),
                -70px 90px 0 0 rgba(21, 81, 61, 0.08),
                60px -52px 0 -1px rgba(211, 161, 47, 0.04),
                60px -52px 0 0 rgba(211, 161, 47, 0.1);
            transform: rotate(-2deg);
        }

        .inventory-login * {
            box-sizing: border-box;
            letter-spacing: 0;
        }

        .inventory-login a {
            color: inherit;
            text-decoration: none;
        }

        .inventory-login__sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .inventory-login__icon {
            width: 22px;
            height: 22px;
            flex: 0 0 auto;
        }

        .inventory-login__shell {
            width: min(1180px, 100%);
            min-height: min(760px, calc(100svh - 56px));
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(400px, 0.78fr);
            gap: 18px;
        }

        .inventory-login__story {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            background:
                linear-gradient(145deg, rgba(11, 47, 36, 0.98), rgba(21, 81, 61, 0.94)),
                repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.05) 0 1px, transparent 1px 72px),
                repeating-linear-gradient(0deg, rgba(244, 196, 48, 0.06) 0 1px, transparent 1px 72px);
            background-position: center;
            background-size: cover;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 34px;
            box-shadow: 0 24px 72px rgba(21, 81, 61, 0.2);
        }

        .inventory-login__story::after {
            content: "";
            position: absolute;
            inset: auto 34px 186px 34px;
            height: 1px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.28), rgba(255, 255, 255, 0));
        }

        .inventory-login__story-top,
        .inventory-login__story-main,
        .inventory-login__metrics {
            position: relative;
            z-index: 1;
        }

        .inventory-login__story-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .inventory-login__brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .inventory-login__mark {
            width: 46px;
            height: 46px;
            border: 1px solid rgba(244, 196, 48, 0.38);
            border-radius: 8px;
            background: rgba(244, 196, 48, 0.14);
            color: var(--yellow-100);
            display: grid;
            place-items: center;
            flex: 0 0 auto;
            font-weight: 950;
        }

        .inventory-login__brand-title,
        .inventory-login__brand-subtitle {
            display: block;
        }

        .inventory-login__brand-title {
            color: #ffffff;
            font-size: 1rem;
            font-weight: 950;
            line-height: 1.1;
        }

        .inventory-login__brand-subtitle {
            color: rgba(255, 255, 255, 0.68);
            font-size: 0.86rem;
            margin-top: 4px;
        }

        .inventory-login__status {
            min-height: 44px;
            border: 1px solid rgba(244, 196, 48, 0.3);
            border-radius: 8px;
            background: rgba(244, 196, 48, 0.13);
            color: var(--yellow-400);
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 0 12px;
            font-size: 0.78rem;
            font-weight: 900;
            white-space: nowrap;
        }

        .inventory-login__story-main {
            max-width: 640px;
            padding: 74px 0 52px;
        }

        .inventory-login__eyebrow {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(244, 196, 48, 0.34);
            border-radius: 8px;
            background: rgba(244, 196, 48, 0.13);
            color: var(--yellow-400);
            padding: 10px;
            font-size: 0.84rem;
            font-weight: 850;
        }

        .inventory-login h1 {
            color: #ffffff;
            font-size: 3.25rem;
            font-weight: 950;
            line-height: 1.03;
            margin: 18px 0 18px;
        }

        .inventory-login__lead {
            max-width: 560px;
            color: rgba(255, 255, 255, 0.76);
            font-size: 1rem;
            line-height: 1.72;
            margin: 0;
        }

        .inventory-login__metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .inventory-login__metric {
            min-height: 104px;
            border: 1px solid rgba(244, 196, 48, 0.22);
            border-radius: 8px;
            background: rgba(255, 243, 191, 0.12);
            backdrop-filter: blur(14px);
            padding: 16px;
            display: grid;
            align-content: space-between;
            gap: 18px;
        }

        .inventory-login__metric-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: rgba(244, 196, 48, 0.18);
            color: var(--yellow-400);
        }

        .inventory-login__metric-icon .inventory-login__icon {
            width: 20px;
            height: 20px;
        }

        .inventory-login__metric strong,
        .inventory-login__metric span {
            display: block;
        }

        .inventory-login__metric strong {
            color: #ffffff;
            font-size: 1.02rem;
            font-weight: 950;
            line-height: 1.1;
        }

        .inventory-login__metric span {
            color: rgba(255, 255, 255, 0.67);
            font-size: 0.8rem;
            font-weight: 750;
            line-height: 1.4;
            margin-top: 10px;
        }

        .inventory-login__form-wrap {
            border: 1px solid rgba(20, 63, 57, 0.12);
            border-radius: 8px;
            background: rgba(255, 253, 242, 0.84);
            box-shadow: 0 24px 72px rgba(21, 81, 61, 0.14);
            backdrop-filter: blur(20px);
            display: grid;
            align-items: center;
            padding: 26px;
        }

        .inventory-login__form-card {
            width: 100%;
            max-width: 470px;
            margin: 0 auto;
        }

        .inventory-login__form-head {
            margin-bottom: 28px;
        }

        .inventory-login__form-kicker {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            background: var(--yellow-100);
            color: var(--green-800);
            padding: 8px;
            font-size: 0.78rem;
            font-weight: 900;
            margin: 0 0 14px;
        }

        .inventory-login__form-kicker .inventory-login__icon {
            width: 18px;
            height: 18px;
        }

        .inventory-login__form-title {
            color: var(--green-950);
            font-size: 2rem;
            line-height: 1.14;
            font-weight: 950;
            margin: 0;
        }

        .inventory-login__form-copy {
            color: #66736c;
            font-size: 0.94rem;
            line-height: 1.66;
            margin: 12px 0 0;
        }

        .inventory-login__form-card .fi-fo-field-wrp {
            gap: 0.45rem;
        }

        .inventory-login__form-card .fi-fo-field-wrp-label span {
            color: var(--green-950);
            font-weight: 850;
        }

        .inventory-login__form-card .fi-input-wrp {
            min-height: 50px;
            border-radius: 8px;
            border-color: rgba(21, 81, 61, 0.16);
            background: rgba(255, 255, 255, 0.74);
            box-shadow: none;
            transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
        }

        .inventory-login__form-card .fi-input-wrp:hover {
            background: #ffffff;
            border-color: rgba(211, 161, 47, 0.42);
        }

        .inventory-login__form-card .fi-input-wrp:focus-within {
            background: #ffffff;
            border-color: var(--gold-500);
            box-shadow: 0 0 0 4px rgba(244, 196, 48, 0.18);
        }

        .inventory-login__form-card .fi-input {
            font-size: 0.95rem;
        }

        .inventory-login__form-card .fi-input-wrp.fi-fo-text-input:has(.fi-input.fi-revealable) {
            min-height: 52px;
        }

        .inventory-login__form-card .fi-input-wrp:has(.fi-input.fi-revealable) .fi-input-wrp-suffix {
            border-left: 1px solid rgba(21, 81, 61, 0.13);
            padding-inline: 12px 14px;
        }

        .inventory-login__form-card .fi-input-wrp:has(.fi-input.fi-revealable) .fi-input-wrp-actions {
            display: flex;
            align-items: center;
        }

        .inventory-login__form-card .fi-input-wrp:has(.fi-input.fi-revealable) .fi-icon-btn {
            width: auto;
            min-width: 92px;
            min-height: 36px;
            border-radius: 8px;
            padding-inline: 10px;
            color: #66736c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .inventory-login__form-card .fi-input-wrp:has(.fi-input.fi-revealable) .fi-icon-btn svg {
            display: none;
        }

        .inventory-login__form-card .fi-input-wrp:has(.fi-input.fi-revealable) .fi-icon-btn:hover {
            background: rgba(244, 196, 48, 0.13);
            color: var(--green-800);
        }

        .inventory-login__form-card .fi-input-wrp:has(.fi-input.fi-revealable) .fi-icon-btn[x-show="! isPasswordRevealed"]::after {
            content: "Tampilkan";
            font-size: 0.82rem;
            font-weight: 850;
        }

        .inventory-login__form-card .fi-input-wrp:has(.fi-input.fi-revealable) .fi-icon-btn[x-show="isPasswordRevealed"]::after {
            content: "Sembunyikan";
            font-size: 0.82rem;
            font-weight: 850;
        }

        .inventory-login__form-card .fi-btn {
            min-height: 50px;
            border-radius: 8px;
            background: var(--green-800);
            color: #ffffff;
            font-weight: 950;
            box-shadow: 0 14px 30px rgba(21, 81, 61, 0.24);
            transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease;
        }

        .inventory-login__form-card .fi-btn:hover {
            background: var(--green-900);
            box-shadow: 0 18px 36px rgba(21, 81, 61, 0.28);
            transform: translateY(-1px);
        }

        .inventory-login__form-card .fi-btn .fi-icon {
            color: var(--yellow-400);
        }

        .inventory-login__form-card .fi-checkbox-input {
            border-radius: 6px;
        }

        .inventory-login__support {
            border-top: 1px solid rgba(21, 81, 61, 0.14);
            color: #66736c;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 26px;
            padding-top: 18px;
        }

        .inventory-login__support-mark {
            min-height: 40px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            background: var(--yellow-100);
            color: var(--green-800);
            padding: 0 12px;
            font-size: 0.82rem;
            font-weight: 900;
        }

        .inventory-login__support a {
            min-height: 42px;
            border: 1px solid rgba(21, 81, 61, 0.16);
            border-radius: 8px;
            color: var(--green-800);
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 0 12px;
            font-size: 0.82rem;
            font-weight: 900;
            transition: background 160ms ease, border-color 160ms ease, color 160ms ease, transform 160ms ease;
        }

        .inventory-login__support a:hover {
            background: var(--gold-500);
            border-color: var(--gold-500);
            color: var(--green-950);
            transform: translateY(-1px);
        }

        @media (max-width: 980px) {
            .inventory-login {
                padding: 18px;
            }

            .inventory-login__shell {
                min-height: auto;
                grid-template-columns: 1fr;
            }

            .inventory-login__story {
                min-height: 560px;
                padding: 28px;
            }

            .inventory-login__story-main {
                padding: 58px 0 44px;
            }

            .inventory-login h1 {
                font-size: 2.55rem;
            }

            .inventory-login__form-wrap {
                padding: 28px;
            }
        }

        @media (max-width: 640px) {
            .inventory-login {
                padding: 12px;
            }

            .inventory-login__story {
                min-height: auto;
                padding: 22px;
                gap: 34px;
            }

            .inventory-login__story::after {
                display: none;
            }

            .inventory-login__story-main {
                padding: 24px 0 8px;
            }

            .inventory-login h1 {
                font-size: 2.05rem;
            }

            .inventory-login__lead {
                font-size: 0.94rem;
            }

            .inventory-login__metrics {
                grid-template-columns: 1fr;
            }

            .inventory-login__metric {
                min-height: auto;
            }

            .inventory-login__form-wrap {
                padding: 22px;
            }

            .inventory-login__form-title {
                font-size: 1.65rem;
            }

            .inventory-login__support {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <div class="inventory-login__shell">
        <section class="inventory-login__story" aria-label="Informasi sistem inventaris">
            <div class="inventory-login__story-top">
                <a class="inventory-login__brand" href="{{ url('/') }}" aria-label="Inventaris Diskebud">
                    <span class="inventory-login__mark">ID</span>
                    <span>
                        <span class="inventory-login__brand-title">Inventaris Diskebud</span>
                        <span class="inventory-login__brand-subtitle">Dashboard aset internal</span>
                    </span>
                </a>

                <span class="inventory-login__status" title="Sistem aktif" aria-label="Sistem aktif">
                    <x-filament::icon icon="heroicon-o-signal" class="inventory-login__icon" />
                    <span>Sistem aktif</span>
                </span>
            </div>

            <div class="inventory-login__story-main">
                <span class="inventory-login__eyebrow" title="Akses admin" aria-label="Akses admin">
                    <x-filament::icon icon="heroicon-o-shield-check" class="inventory-login__icon" />
                    <span>Akses admin</span>
                </span>
                <h1>Kontrol inventaris cepat dan siap audit.</h1>
                <p class="inventory-login__lead">
                    Masuk untuk mengelola data barang, memantau kondisi aset, mencatat mutasi, dan menyiapkan laporan periodik dari satu dashboard.
                </p>
            </div>

            <div class="inventory-login__metrics" aria-label="Fitur utama">
                <div class="inventory-login__metric">
                    <span class="inventory-login__metric-icon">
                        <x-filament::icon icon="heroicon-o-cube" class="inventory-login__icon" />
                    </span>
                    <strong>KIB B</strong>
                </div>
                <div class="inventory-login__metric">
                    <span class="inventory-login__metric-icon">
                        <x-filament::icon icon="heroicon-o-qr-code" class="inventory-login__icon" />
                    </span>
                    <strong>QR Aset</strong>
                </div>
                <div class="inventory-login__metric">
                    <span class="inventory-login__metric-icon">
                        <x-filament::icon icon="heroicon-o-document-chart-bar" class="inventory-login__icon" />
                    </span>
                    <strong>Laporan</strong>
                </div>
            </div>
        </section>

        <section class="inventory-login__form-wrap" aria-label="Form login dashboard">
            <div class="inventory-login__form-card">
                <div class="inventory-login__form-head">
                    <p class="inventory-login__form-kicker" title="Dashboard" aria-label="Dashboard">
                        <x-filament::icon icon="heroicon-o-squares-2x2" class="inventory-login__icon" />
                        <span>Area login</span>
                    </p>
                    <h2 class="inventory-login__form-title">{{ $this->getHeading() }}</h2>
                    <p class="inventory-login__form-copy">{{ $this->getSubheading() }}</p>
                </div>

                {{ $this->content }}

                <div class="inventory-login__support">
                    <span class="inventory-login__support-mark" title="Inventaris Diskebud" aria-label="Inventaris Diskebud">
                        <x-filament::icon icon="heroicon-o-building-office-2" class="inventory-login__icon" />
                        <span>Diskebud</span>
                    </span>
                    <a href="{{ url('/') }}" title="Kembali ke halaman depan" aria-label="Kembali ke halaman depan">
                        <x-filament::icon icon="heroicon-o-home" class="inventory-login__icon" />
                        <span>Beranda</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>
