<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestasiModel extends Model
{
    use HasFactory;

    protected $table = 'investasi_models';
    protected $primaryKey = 'id_investasi';
    protected $fillable = [
        'id_user',
        'id_proyek',
        'total_investasi',
        'tanggal',
        'bukti_transfer',
        'is_verified',
    ];

    // Definisi relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Definisi relasi dengan model Proyek
    public function proyek()
    {
        return $this->belongsTo(ProyekModel::class, 'id_proyek', 'id_proyek');
    }
}
