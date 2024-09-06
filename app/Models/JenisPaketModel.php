<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPaketModel extends Model
{
    use HasFactory;

    protected $table = 'jenis_paket_models';
    protected $primaryKey = 'id_paket';

    public function proyek()
    {
        return $this->belongsTo(ProyekModel::class, 'id_proyek');
    }
    
}
