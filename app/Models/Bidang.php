<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bidang extends Model
{
    protected $table = 'bidang';

    protected $fillable = [
        'nama_bidang',
    ];

    public function ruangans(): BelongsToMany
    {
        return $this->belongsToMany(Ruangan::class, 'bidang_ruangan')
            ->withTimestamps();
    }

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
