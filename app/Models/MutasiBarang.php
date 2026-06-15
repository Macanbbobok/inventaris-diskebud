<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiBarang extends Model
{
    protected $table = 'mutasi_barang';

    protected $fillable = [
        'barang_id',
        'ruangan_asal_id',
        'ruangan_tujuan_id',
        'tanggal_mutasi',
        'alasan',
        'kondisi_sebelum',
        'kondisi_sesudah',
        'dilakukan_oleh',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function ruanganAsal(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_asal_id');
    }

    public function ruanganTujuan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_tujuan_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilakukan_oleh');
    }
}
