<?php

namespace App\Models;

use App\Services\RequestKerusakanNotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestKerusakan extends Model
{
    protected $table = 'request_kerusakan';

    protected $fillable = [
        'barang_id',
        'tanggal_laporan',
        'tingkat_kerusakan',
        'deskripsi_kerusakan',
        'status',
        'dilaporkan_oleh',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dilaporkan_oleh');
    }

    public function pemeliharaan(): HasOne
    {
        return $this->hasOne(Pemeliharaan::class);
    }

    protected static function booted(): void
    {
        static::created(function (RequestKerusakan $request): void {
            $request->updateBarangCondition();

            app(RequestKerusakanNotificationService::class)
                ->sendCreatedNotification($request);
        });

        static::updated(function (RequestKerusakan $request): void {
            if ($request->wasChanged(['barang_id', 'tingkat_kerusakan', 'status'])) {
                $request->updateBarangCondition();
            }

            if ($request->wasChanged('status')) {
                $notificationService = app(RequestKerusakanNotificationService::class);

                $notificationService->sendStatusNotification($request);

                if ($request->status !== 'diajukan') {
                    $notificationService->markCreatedNotificationsAsReadForAdmins($request);
                }
            }
        });
    }

    private function updateBarangCondition(): void
    {
        $status = $this->status ?? 'diajukan';

        if (! in_array($status, ['diajukan', 'diproses'], true)) {
            return;
        }

        $condition = $this->tingkat_kerusakan === 'ringan'
            ? 'rusak_ringan'
            : 'rusak_berat';

        Barang::whereKey($this->barang_id)->update([
            'kondisi' => $condition,
        ]);
    }
}
