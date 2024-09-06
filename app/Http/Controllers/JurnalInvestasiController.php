<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JurnalInvestasiModel;
use App\Models\ProyekModel;
use Illuminate\Support\Facades\DB;

class JurnalInvestasiController extends Controller
{
    public function index()
    {
        $jurnals = JurnalInvestasiModel::join('proyek_models', 'jurnal_investasi_models.id_proyek', '=', 'proyek_models.id_proyek')
            ->select(
                'proyek_models.nama_proyek',
                DB::raw('SUM(jurnal_investasi_models.kredit) as kredit'),
                DB::raw('SUM(jurnal_investasi_models.debit) as debit'),
                DB::raw('(SELECT saldo_akhir FROM jurnal_investasi_models ji 
                          WHERE ji.id_proyek = jurnal_investasi_models.id_proyek 
                          ORDER BY ji.created_at DESC 
                          LIMIT 1) as saldo_akhir'),
                'jurnal_investasi_models.id_proyek'
            )
            ->groupBy('proyek_models.nama_proyek', 'jurnal_investasi_models.id_proyek')
            ->get();
        
        $title = 'Daftar Jurnal Proyek';
        return view('admin.proyek-investasi.jurnal-proyek.index', compact('jurnals', 'title'));
    }    

    public function show($id)
    {
        $proyek = ProyekModel::findOrFail($id);

        $jurnals = JurnalInvestasiModel::where('id_proyek', $id)
            ->get();

        $title = 'Detail Jurnal Proyek';
        return view('admin.proyek-investasi.jurnal-proyek.detail_index', compact('proyek', 'jurnals', 'title'));
    }
}