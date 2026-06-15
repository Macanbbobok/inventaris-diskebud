<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use SoftDeletes;

    protected $table = 'ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'lantai',
    ];

    public function bidangs(): BelongsToMany
    {
        return $this->belongsToMany(Bidang::class, 'bidang_ruangan')
            ->withTimestamps();
    }

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class);
    }

    public function mutasiAsal(): HasMany
    {
        return $this->hasMany(MutasiBarang::class, 'ruangan_asal_id');
    }

    public function mutasiTujuan(): HasMany
    {
        return $this->hasMany(MutasiBarang::class, 'ruangan_tujuan_id');
    }
}
