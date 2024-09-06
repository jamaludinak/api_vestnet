<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanModel extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_models';
    protected $primaryKey = 'id_kegiatan';

    protected $fillable = [
        'id_proyek', 
        'nama_kegiatan', 
        'jenis_kegiatan', 
        'total_biaya', 
        'tgl_kegiatan', 
        'foto_nota',
        'foto_kegiatan'
    ];

    public function proyek()
    {
        return $this->belongsTo(ProyekModel::class, 'id_proyek', 'id_proyek');
    }

    public function delete_kegiatans($id)
    {
        return $this->where('id_kegiatan', $id)->delete();
    }

}
