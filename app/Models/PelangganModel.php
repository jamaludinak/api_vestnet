<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelangganModel extends Model
{
    use HasFactory;

    protected $table = 'pelanggan_models';
    protected $primaryKey = 'id_pelanggan_base'; 

    protected $fillable = [
        'id_paket',
        'tanggal_pemasangan',
        'biaya_berlangganan',
        'biaya_pemasangan',
        'nik',
        'foto_ktp',
        'id_pelanggan' 
    ];

    public function jenis_paket()
    {
        return $this->belongsTo(JenisPaketModel::class, 'id_paket');
    }

    public function delete_pelanggan($id)
    {
        return $this->where('id_pelanggan_base', $id)->delete();
    }
}
