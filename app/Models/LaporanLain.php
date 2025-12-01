<?php

namespace App\Models;

use App\Traits\Verifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanLain extends Model
{
    use HasFactory;
    use Verifiable;

    protected $table = 'laporan_lain';
    protected $primaryKey = 'id_laporan';
    protected $fillable = [
        'id_operator',
        'id_pimpinan',
        'tanggal_laporan',
        'judul',
        'deskripsi',

        'narasi_a',
        'file_a',

        'narasi_b',
        'file_b',

        'narasi_c',
        'file_c',

        'narasi_d',
        'file_d',

        'narasi_e',
        'file_e',

        'narasi_f',
        'file_f',

        'narasi_g',
        'file_g',

        'narasi_h',
        'file_h',

        'penutup',

        'status_laporan',
        'catatan',
        'verified_at'
    ];

    protected $casts = [
        'file_a' => 'array',
        'file_b' => 'array',
        'file_c' => 'array',
        'file_d' => 'array',
        'file_e' => 'array',
        'file_f' => 'array',
        'file_g' => 'array',
        'file_h' => 'array',
        'verified_at' => 'datetime',
        'tanggal_laporan' => 'date'
    ];
}
