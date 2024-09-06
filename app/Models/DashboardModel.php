<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Analytics;
use Spatie\Analytics\Period;

class DashboardModel extends Model
{
    use HasFactory;

    public function getUserCount()
    {
        return DB::table('users')->where('role_id', 2)->count();
    }

    public function getInvestorCount()
    {
        return DB::table('mutasi_models')
        ->join('investasi_models', 'mutasi_models.id_investasi', '=', 'investasi_models.id_investasi')
        ->distinct('investasi_models.id_user')
        ->count('investasi_models.id_user');
    }

    public function getPelangganCount()
    {
        return DB::table('pelanggan_models')->count();
    }

    public function getProyekCount()
    {
        return DB::table('proyek_models')->count();
    }

    public function getProyekSelesaiCount()
    {
        return DB::table('proyek_models')->where('status', 'Pendanaan selesai')->count();
    }

    public function getPengajuanDesaCount()
    {
        return DB::table('pengajuan_models')->count();
    }
}