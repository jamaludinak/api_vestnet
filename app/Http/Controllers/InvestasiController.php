<?php

namespace App\Http\Controllers;

use App\Models\InvestasiModel;
use App\Models\JurnalInvestasiModel;
use App\Models\ProyekModel;
use App\Models\MutasiModel;
use App\Models\User;
use Illuminate\Http\Request;

class InvestasiController extends Controller
{
    public function index()
    {
        $title = 'Data Investasi Internet';
        $investasi = InvestasiModel::join('users', 'users.id_user', '=', 'investasi_models.id_user')
            ->join('proyek_models', 'proyek_models.id_proyek', '=', 'investasi_models.id_proyek')
            ->select('investasi_models.id_investasi', 'users.nama_lengkap', 'users.is_verified as verified_user', 'proyek_models.nama_proyek', 'investasi_models.total_investasi', 'investasi_models.tanggal', 'investasi_models.bukti_transfer', 'investasi_models.is_verified as verify')
            ->get();

        return view('admin.investasi-internet.data-investasi.index', compact('title', 'investasi'));
    }

    public function tinjau($id)
    {
        $investasi = InvestasiModel::with('user', 'proyek')->findOrFail($id);
        $user = $investasi->user;

        return view('admin.investasi-internet.data-investasi.tinjau_index', compact('investasi', 'user'));
    }

    public function approve(Request $request, $id)
    {
        $investasi = InvestasiModel::findOrFail($id);
        $investasi->is_verified = 1;
        $investasi->save();
    
        $mutasi_terakhir = MutasiModel::join('investasi_models', 'mutasi_models.id_investasi', '=', 'investasi_models.id_investasi')
                            ->where('investasi_models.id_user', $investasi->id_user)
                            ->orderBy('mutasi_models.created_at', 'desc')
                            ->first();

        $mutasi_baru = new MutasiModel();
        $mutasi_baru->id_investasi = $investasi->id_investasi;

        if ($mutasi_terakhir) {
            $mutasi_baru->saldo_awal = $mutasi_terakhir->saldo_akhir;
        } else {
            $mutasi_baru->saldo_awal = 0;
        }
    
        $mutasi_baru->kredit = $investasi->total_investasi;
        $mutasi_baru->debit = 0;
        $mutasi_baru->saldo_akhir = $mutasi_baru->saldo_awal + $mutasi_baru->kredit;
        $mutasi_baru->keterangan = 'Transaksi investasi oleh investor';
        $mutasi_baru->save();

        $proyek = ProyekModel::findOrFail($investasi->id_proyek);
        $proyek->dana_terkumpul += $investasi->total_investasi;
        if($proyek->dana_terkumpul == $proyek->target_invest){
            $proyek->status = 'Pendanaan selesai';
        } else {  
            $proyek->status = 'Dalam pendanaan';
        }
        $proyek->save();
       
        $jurnal_terakhir = JurnalInvestasiModel::where('id_proyek', $investasi->id_proyek)
        ->orderBy('tanggal', 'desc')
        ->first();

        $jurnal_baru = new JurnalInvestasiModel();
        $jurnal_baru->id_proyek = $investasi->id_proyek;
        $jurnal_baru->tanggal = now()->format('Y-m-d');

        if ($jurnal_terakhir) {
        $jurnal_baru->saldo_awal = $jurnal_terakhir->saldo_akhir;
        } else {
        $jurnal_baru->saldo_awal = 0;
        }

        $jurnal_baru->kredit = $investasi->total_investasi;
        $jurnal_baru->debit = 0;
        $jurnal_baru->saldo_akhir = $jurnal_baru->saldo_awal + $jurnal_baru->kredit;
        $jurnal_baru->keterangan = 'Investasi proyek oleh investor';
        $jurnal_baru->save();
    
        return redirect('administrator/investasi-internet/data-investasi')->with('success', 'Investasi berhasil disetujui.');
    }    

    public function reject($id)
    {
        $investasi = InvestasiModel::findOrFail($id);
        $investasi->delete();

        return redirect('administrator/investasi-internet/data-investasi')->with('success', 'Investasi telah ditolak.');
    }
}
