<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';
    protected $primaryKey = 'id_wilayah';
    protected $fillable = [
        'nama_wilayah',
        'latitude',
        'longitude',
        'kop_surat',
        'tanda_tangan',
        'status_wilayah',
        'status_wilayah_updated_at'
    ];

    protected $casts = [
        'status_wilayah_updated_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_wilayah', 'id_wilayah');
    }
}
