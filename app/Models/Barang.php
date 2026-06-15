<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Barang extends Model
{
    use SoftDeletes;

    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'jenis_kib',
        'bidang_id',
        'ruangan_id',
        'tahun_perolehan',
        'tanggal_perolehan',
        'kondisi',
        'status',
        'harga_perolehan',
        'qr_code',
        'foto',
        'created_by',
    ];

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function detailKibB(): HasOne
    {
        return $this->hasOne(KibBPeralatanMesin::class);
    }

    public function mutasiBarang(): HasMany
    {
        return $this->hasMany(MutasiBarang::class);
    }

    public function pemeliharaan(): HasMany
    {
        return $this->hasMany(Pemeliharaan::class);
    }

    public function requestKerusakan(): HasMany
    {
        return $this->hasMany(RequestKerusakan::class);
    }

    public function getKodeNamaAttribute(): string
    {
        return "{$this->kode_barang} - {$this->nama_barang}";
    }

    protected function casts(): array
    {
        return [
            'tanggal_perolehan' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Barang $barang): void {
            if ($barang->tanggal_perolehan) {
                $barang->tahun_perolehan = Carbon::parse($barang->tanggal_perolehan)->year;
            }
        });

        static::saved(function (Barang $barang) {
            if ($barang->bidang_id && $barang->ruangan_id) {
                $barang->ruangan?->bidangs()->syncWithoutDetaching([
                    $barang->bidang_id,
                ]);
            }
        });

        static::created(function (Barang $barang) {
            $qrValue = route('barang.public.show', $barang->id);

            $fileName = 'qrcodes/barang-'.$barang->id.'.svg';

            QrCode::format('svg')
                ->size(300)
                ->generate($qrValue, storage_path('app/public/'.$fileName));

            $barang->update([
                'qr_code' => $fileName,
            ]);
        });
    }
}
