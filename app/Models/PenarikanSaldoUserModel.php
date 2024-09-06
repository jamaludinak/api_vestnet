<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenarikanSaldoUserModel extends Model
{
    use HasFactory;

    protected $table = 'penarikan_saldo_user_models';
    protected $primaryKey = 'id_penarikan_saldo';

    protected $fillable = [
        'id_investasi', 
        'tanggal_pengajuan',
        'jumlah',
        'tanggal_acc',
        'bukti_transfer',
        'is_verified'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function investasi()
    {
        return $this->belongsTo(InvestasiModel::class, 'id_investasi', 'id_investasi');
    }

    public function mutasi()
    {
        return $this->hasMany(MutasiModel::class, 'id_investasi', 'id_investasi');
    }
}
