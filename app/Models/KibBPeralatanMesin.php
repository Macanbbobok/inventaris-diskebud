<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KibBPeralatanMesin extends Model
{
    protected $table = 'kib_b_peralatan_mesin';

    protected $fillable = [
        'barang_id',
        'merk_type',
        'ukuran',
        'bahan',
        'no_seri',
        'spesifikasi',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    protected static function booted(): void
    {
        static::created(function (KibBPeralatanMesin $detail): void {
            $detail->markBarangAsKibB();
        });

        static::updated(function (KibBPeralatanMesin $detail): void {
            if ($detail->wasChanged('barang_id')) {
                $detail->unmarkBarangAsKibB((int) $detail->getOriginal('barang_id'));
            }

            $detail->markBarangAsKibB();
        });

        static::deleted(function (KibBPeralatanMesin $detail): void {
            $detail->unmarkBarangAsKibB((int) $detail->barang_id);
        });
    }

    private function markBarangAsKibB(): void
    {
        if (! $this->barang_id) {
            return;
        }

        Barang::whereKey($this->barang_id)->update([
            'jenis_kib' => 'B',
        ]);
    }

    private function unmarkBarangAsKibB(int $barangId): void
    {
        if ($barangId < 1) {
            return;
        }

        $hasOtherKibBDetail = static::query()
            ->where('barang_id', $barangId)
            ->exists();

        if (! $hasOtherKibBDetail) {
            Barang::whereKey($barangId)->update([
                'jenis_kib' => null,
            ]);
        }
    }
}
