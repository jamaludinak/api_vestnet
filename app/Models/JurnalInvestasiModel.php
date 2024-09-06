<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalInvestasiModel extends Model
{
    use HasFactory;

    protected $table = 'jurnal_investasi_models';
    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'id_proyek', 
        'saldo_awal',
        'kredit',
        'debit',
        'saldo_akhir',
        'keterangan',
    ];

    public function proyek()
    {
        return $this->belongsTo(ProyekModel::class, 'id_proyek', 'id_proyek');
    }
}
