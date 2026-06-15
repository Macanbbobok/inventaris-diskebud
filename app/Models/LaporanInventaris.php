<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanInventaris extends Model
{
    protected $table = 'laporan_inventaris';

    protected $fillable = [
        'jenis_laporan',
        'periode',
        'bulan',
        'tahun',
        'file_laporan',
        'dibuat_oleh',
    ];

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
