<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait Verifiable
{
    // Definisikan konstanta untuk status agar lebih mudah dikelola
    public const STATUS_PENDING = 'menunggu verifikasi';
    public const STATUS_REVISION = 'revisi';
    public const STATUS_APPROVED = 'disetujui';

    /**
     * Boot the trait.
     * Otomatis set status awal saat model dibuat.
     */
    protected static function bootVerifiable()
    {
        static::creating(function ($model) {
            // Pastikan model punya kolom status_laporan (atau nama kolom status Anda)
            if (empty($model->status_laporan)) {
                $model->status_laporan = self::STATUS_PENDING;
            }
        });
    }

    /**
     * Relasi ke User yang membuat laporan (Operator).
     * Pastikan model Anda memiliki foreign key 'id_operator'.
     */
    public function operator(): BelongsTo
    {
        // Sesuaikan 'id_operator' jika nama kolom berbeda
        return $this->belongsTo(User::class, 'id_operator', 'id_users');
    }

    /**
     * Relasi ke User yang melakukan verifikasi (Pimpinan).
     * Pastikan model Anda memiliki foreign key 'id_pimpinan'.
     */
    public function pimpinan(): BelongsTo
    {
        // Sesuaikan 'id_pimpinan' jika nama kolom berbeda
        return $this->belongsTo(User::class, 'id_pimpinan', 'id_users');
    }

    /**
     * Scope untuk mendapatkan laporan yang menunggu verifikasi.
     */
    public function scopePendingVerification($query)
    {
        return $query->where('status_laporan', self::STATUS_PENDING);
    }

    /**
     * Scope untuk mendapatkan laporan yang perlu direvisi.
     */
    public function scopeNeedsRevision($query)
    {
        return $query->where('status_laporan', self::STATUS_REVISION);
    }

    /**
     * Scope untuk mendapatkan laporan yang sudah disetujui.
     */
    public function scopeApproved($query)
    {
        return $query->where('status_laporan', self::STATUS_APPROVED);
    }

    /**
     * Menandai laporan sebagai disetujui.
     */
    public function approve(?User $pimpinan = null): bool
    {
        $pimpinan = $pimpinan ?? Auth::user();
        if (!$pimpinan) {
            return false; // Tidak bisa approve tanpa pimpinan
        }

        $this->status_laporan = self::STATUS_APPROVED;
        $this->id_pimpinan = $pimpinan->id_users; // Sesuaikan jika primary key User bukan id_users
        $this->catatan = null; // Hapus catatan revisi jika ada
        $this->verified_at = now();
        return $this->save();
    }

    /**
     * Meminta revisi untuk laporan.
     */
    public function requestRevision(string $notes, ?User $pimpinan = null): bool
    {
        $pimpinan = $pimpinan ?? Auth::user();
        if (!$pimpinan) {
            return false; // Tidak bisa revisi tanpa pimpinan
        }

        $this->status_laporan = self::STATUS_REVISION;
        $this->id_pimpinan = $pimpinan->id_users; // Sesuaikan jika primary key User bukan id_users
        $this->catatan = $notes;
        $this->verified_at = now();
        return $this->save();
    }

    /**
     * Cek apakah laporan sedang menunggu verifikasi.
     */
    public function isPending(): bool
    {
        return $this->status_laporan === self::STATUS_PENDING;
    }

    /**
     * Cek apakah laporan perlu direvisi.
     */
    public function needsRevision(): bool
    {
        return $this->status_laporan === self::STATUS_REVISION;
    }

    /**
     * Cek apakah laporan sudah disetujui.
     */
    public function isApproved(): bool
    {
        return $this->status_laporan === self::STATUS_APPROVED;
    }
}
