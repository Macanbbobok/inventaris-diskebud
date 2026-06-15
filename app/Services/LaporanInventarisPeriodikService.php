<?php

namespace App\Services;

use App\Models\LaporanInventaris;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class LaporanInventarisPeriodikService
{
    private const JENIS_LAPORAN_OTOMATIS = 'gabungan';

    public function __construct(
        private readonly LaporanInventarisGenerator $generator,
    ) {}

    public function generateDueReports(CarbonInterface|string|null $date = null, bool $force = false): array
    {
        $periods = $this->duePeriods($date);
        $created = [];
        $skipped = [];

        foreach ($periods as $period) {
            $data = [
                'jenis_laporan' => self::JENIS_LAPORAN_OTOMATIS,
                'periode' => $period['periode'],
                'bulan' => $period['bulan'],
                'tahun' => $period['tahun'],
                'dibuat_oleh' => null,
            ];

            if (! $force && $this->reportExists($data)) {
                $skipped[] = [
                    'period' => $period,
                    'reason' => 'exists',
                ];

                continue;
            }

            $created[] = [
                'period' => $period,
                'record' => $this->generator->generate($data),
            ];
        }

        return [
            'due' => $periods,
            'created' => $created,
            'skipped' => $skipped,
        ];
    }

    public function duePeriods(CarbonInterface|string|null $date = null): array
    {
        $referenceDate = CarbonImmutable::parse($date ?? now())->startOfDay();

        if ($referenceDate->day !== 1) {
            return [];
        }

        $periods = [];
        $previousMonth = $referenceDate->subMonthNoOverflow();

        $periods[] = $this->period(
            'bulanan',
            $previousMonth->month,
            $previousMonth->year,
        );

        if (in_array($referenceDate->month, [1, 4, 7, 10], true)) {
            $quarterEnd = $referenceDate->subDay();
            $quarterStartMonth = (int) (floor(($quarterEnd->month - 1) / 3) * 3 + 1);

            $periods[] = $this->period(
                'triwulanan',
                $quarterStartMonth,
                $quarterEnd->year,
            );
        }

        if (in_array($referenceDate->month, [1, 7], true)) {
            $semesterStartMonth = $referenceDate->month === 7 ? 1 : 7;
            $semesterYear = $referenceDate->month === 7
                ? $referenceDate->year
                : $referenceDate->year - 1;

            $periods[] = $this->period(
                'semesteran',
                $semesterStartMonth,
                $semesterYear,
            );
        }

        if ($referenceDate->month === 1) {
            $periods[] = $this->period(
                'tahunan',
                null,
                $referenceDate->year - 1,
            );
        }

        return $periods;
    }

    private function reportExists(array $data): bool
    {
        return LaporanInventaris::query()
            ->where('jenis_laporan', $data['jenis_laporan'])
            ->where('periode', $data['periode'])
            ->where('tahun', $data['tahun'])
            ->when(
                $data['bulan'] === null,
                fn ($query) => $query->whereNull('bulan'),
                fn ($query) => $query->where('bulan', $data['bulan']),
            )
            ->exists();
    }

    private function period(string $periode, ?int $bulan, int $tahun): array
    {
        return [
            'periode' => $periode,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'label' => $this->periodLabel($periode, $bulan, $tahun),
        ];
    }

    private function periodLabel(string $periode, ?int $bulan, int $tahun): string
    {
        return match ($periode) {
            'bulanan' => 'Bulanan ' . $this->monthName($bulan) . ' ' . $tahun,
            'triwulanan' => 'Triwulan ' . $this->romanNumber((int) ceil($bulan / 3)) . ' ' . $tahun,
            'semesteran' => 'Semester ' . ($bulan === 1 ? 'I' : 'II') . ' ' . $tahun,
            'tahunan' => 'Tahunan ' . $tahun,
            default => ucfirst($periode) . ' ' . $tahun,
        };
    }

    private function monthName(?int $month): string
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ][$month] ?? '-';
    }

    private function romanNumber(int $number): string
    {
        return [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
        ][$number] ?? (string) $number;
    }
}
