<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportLog extends Model
{
    protected $table = 'import_log';

    protected $fillable = [
        'nama_file',
        'tipe_import',
        'jenis_kib',
        'total_baris',
        'berhasil',
        'gagal',
        'duplikat',
        'status',
        'catatan_error',
        'detail_error',
        'path_file',
        'waktu_selesai',
        'diupload_oleh',
    ];

    protected $casts = [
        'detail_error' => 'array',
        'waktu_selesai' => 'datetime',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diupload_oleh');
    }
}
