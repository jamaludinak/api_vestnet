<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumentasiModel extends Model
{
    use HasFactory;

    protected $table = 'dokumentasi_models';

    protected $fillable = [
        'id_proyek',
        'file_url'
    ];

    public function proyek()
    {
        return $this->belongsTo(ProyekModel::class, 'id_proyek', 'id_proyek');
    }
}
