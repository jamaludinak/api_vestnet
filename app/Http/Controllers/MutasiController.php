<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\InvestasiModel;
use App\Models\MutasiModel;
use DB;

class MutasiController extends Controller
{
    public function index()
    {
        $investors = DB::table('investasi_models')
            ->join('users', 'investasi_models.id_user', '=', 'users.id_user')
            ->join('mutasi_models', 'investasi_models.id_investasi', '=', 'mutasi_models.id_investasi')
            ->select(
                'users.id_user',
                'users.nama_lengkap',
                DB::raw('SUM(mutasi_models.kredit) as kredit'),
                DB::raw('SUM(mutasi_models.debit) as debit'),
                DB::raw('(SELECT saldo_akhir FROM mutasi_models m 
                          JOIN investasi_models im ON im.id_investasi = m.id_investasi 
                          WHERE im.id_user = users.id_user 
                          ORDER BY m.created_at DESC LIMIT 1) as saldo_akhir'),
                DB::raw('COUNT(DISTINCT investasi_models.id_proyek) as proyek_count')
            )
            ->groupBy('users.id_user', 'users.nama_lengkap')
            ->get();
    
        $title = 'Daftar Investor';
        return view('admin.investasi-internet.mutasi-investor.index', compact('investors', 'title'));
    }          

    public function show($id)
    {
        $user = User::findOrFail($id);
    
        $proyek_investasi = InvestasiModel::where('id_user', $id)
            ->where('is_verified', 1) 
            ->join('proyek_models', 'investasi_models.id_proyek', '=', 'proyek_models.id_proyek')
            ->select('proyek_models.nama_proyek')
            ->distinct() 
            ->get();
    
        $mutasi = MutasiModel::whereHas('investasi', function ($query) use ($id) {
            $query->where('id_user', $id);
            $query->where('is_verified', 1);
        })->get();
    
        $title = 'Detail Data Investor';
        return view('admin.investasi-internet.mutasi-investor.detail_index', compact('user', 'mutasi', 'proyek_investasi', 'title'));
    }
    
}
