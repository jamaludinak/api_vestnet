<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenarikanSaldoUserModel;
use App\Models\MutasiModel;
use App\Models\InvestasiModel;
use Illuminate\Support\Carbon;

class PenarikanSaldoUserController extends Controller
{
    public function index()
    {
        $penarikan = PenarikanSaldoUserModel::with(['user', 'investasi'])
            ->get()
            ->map(function ($penarikan) {
                $mutasi_terakhir = MutasiModel::where('id_investasi', $penarikan->id_investasi)
                    ->orderBy('created_at', 'desc')
                    ->first();
                $penarikan->saldo_akhir = $mutasi_terakhir ? $mutasi_terakhir->saldo_akhir : 0;
                return $penarikan;
            });

        return view('admin.investasi-internet.penarikan-saldo.index', compact('penarikan'))
            ->with('title', 'Pengajuan Penarikan Saldo');
    }

    public function peninjauan($id)
    {
        $penarikan = PenarikanSaldoUserModel::with('user')->findOrFail($id);

        $mutasi_terakhir = MutasiModel::where('id_investasi', $penarikan->id_investasi)
            ->orderBy('created_at', 'desc')
            ->first();
            
        $penarikan->saldo_akhir = $mutasi_terakhir ? $mutasi_terakhir->saldo_akhir : 0;
    
        return view('admin.investasi-internet.penarikan-saldo.tinjau_index', compact('penarikan'))
            ->with('title', 'Peninjauan Penarikan Saldo');
    }    

    public function approve(Request $request, $id)
    {
        $penarikan = PenarikanSaldoUserModel::with('investasi')->findOrFail($id);

        $mutasi_terakhir = MutasiModel::join('investasi_models', 'mutasi_models.id_investasi', '=', 'investasi_models.id_investasi')
                            ->where('investasi_models.id_user', $penarikan->investasi->id_user)
                            ->orderBy('mutasi_models.created_at', 'desc')
                            ->first();

        if ($mutasi_terakhir && $mutasi_terakhir->saldo_akhir >= $penarikan->jumlah) {
            
            $request->validate([
                'bukti_transfer' => 'required|file|mimes:png,jpg,jpeg,heic,pdf|max:2048',
            ]);
        
            $penarikan->tanggal_acc = now();
            $penarikan->is_verified = 1;
            $penarikan->bukti_transfer = $request->file('bukti_transfer')->store('bukti_transfer');
            $penarikan->save();

            $mutasi_baru = new MutasiModel();
            $mutasi_baru->id_investasi = $penarikan->id_investasi;
            $mutasi_baru->saldo_awal = $mutasi_terakhir->saldo_akhir;
            $mutasi_baru->kredit = 0;
            $mutasi_baru->debit = $penarikan->jumlah;
            $mutasi_baru->saldo_akhir = $mutasi_baru->saldo_awal - $mutasi_baru->debit;
            $mutasi_baru->keterangan = 'Penarikan saldo oleh user';
            $mutasi_baru->save();

            return redirect('administrator/investasi-internet/penarikan-saldo')->with('success', 'Penarikan saldo disetujui.');
        } else {
            return redirect('administrator/investasi-internet/penarikan-saldo')->with('error', 'Saldo akhir tidak mencukupi untuk penarikan.');
        }
    }    

    public function reject($id)
    {
        $penarikan = PenarikanSaldoUserModel::findOrFail($id);
        $penarikan->delete();

        return redirect('administrator/investasi-internet/penarikan-saldo')->with('success', 'Penarikan saldo berhasil ditolak.');
    }
}
