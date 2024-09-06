<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiModel extends Model
{
    use HasFactory;

    protected $table = 'mutasi_models';
    protected $primaryKey = 'id_investasi';

    protected $fillable = [
        'id_investasi',
        'saldo_awal',
        'kredit',
        'debit',
        'saldo_akhir',
        'keterangan',
    ];

    public function investasi()
    {
        return $this->belongsTo(InvestasiModel::class, 'id_investasi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
