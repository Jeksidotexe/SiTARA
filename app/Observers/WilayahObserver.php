<?php

namespace App\Observers;

use App\Models\Wilayah;

class WilayahObserver
{
    /**
     * Handle peristiwa "saving" (created atau updated) model.
     *
     * @param  \App\Models\Wilayah  $wilayah
     * @return void
     */
    public function saving(Wilayah $wilayah)
    {
        // Cek jika kolom 'status_wilayah' diubah (isDirty)
        // Ini penting agar timestamp tidak ter-update jika Pimpinan hanya mengubah nama wilayah.
        if ($wilayah->isDirty('status_wilayah')) {
            $wilayah->status_wilayah_updated_at = now();
        }
    }
}
