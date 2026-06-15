<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemeliharaan extends Model
{
    protected $table = 'pemeliharaan';

    protected $fillable = [
        'barang_id',
        'request_kerusakan_id',
        'tanggal',
        'jenis_pemeliharaan',
        'deskripsi',
        'biaya',
        'kondisi_sesudah',
        'dilakukan_oleh',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }

    public function requestKerusakan(): BelongsTo
    {
        return $this->belongsTo(RequestKerusakan::class);
    }

    protected static function booted(): void
    {
        static::created(function (Pemeliharaan $pemeliharaan): void {
            $pemeliharaan->barang?->update([
                'kondisi' => $pemeliharaan->kondisi_sesudah,
            ]);

            $pemeliharaan->requestKerusakan?->update([
                'status' => 'selesai',
            ]);
        });
    }
}
