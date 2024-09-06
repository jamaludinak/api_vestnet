<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagihanModel extends Model
{
    use HasFactory;

    protected $table = 'tagihan_models';
    protected $primaryKey = 'id_tagihan';

    protected $fillable = [
        'id_pelanggan',
        'tanggal',
        'tagihan',
        'metode_pembayaran',
        'bukti_pembayaran',
        'is_verified'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(PelangganModel::class, 'id_pelanggan', 'id_pelanggan');
    }
}
