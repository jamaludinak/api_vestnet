<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembelianAsetModel;
use App\Models\ProyekModel;
use App\Models\JurnalInvestasiModel;
use App\Models\DetailPembelianAsetModel;
use Illuminate\Support\Facades\Session;

class PembelianAsetController extends Controller
{
    public function index()
    {
        $title = 'Daftar Pembelian Aset';

        $pembelianAset = PembelianAsetModel::with('proyek')->get();

        return view('admin.pengelolaan.pembelian-aset.index', compact('title', 'pembelianAset'));
    }

    public function create()
    {
        $title = 'Tambah Pembelian Aset';
        $proyek = ProyekModel::all();

        return view('admin.pengelolaan.pembelian-aset.create_index', compact('title', 'proyek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_proyek' => 'required|exists:proyek_models,id_proyek',
            'nama_aset' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'masa_manfaat' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'biaya_satuan' => 'required|numeric',
            'bukti_pembayaran' => 'required|mimes:jpg,jpeg,png,heic|max:2048',
        ]);
    
        $data = $request->all();
        $data['total_biaya'] = $data['jumlah'] * $data['biaya_satuan'];
    
        $jurnal_terakhir = JurnalInvestasiModel::where('id_proyek', $request->id_proyek)
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($jurnal_terakhir && $jurnal_terakhir->saldo_akhir >= $data['total_biaya']) {
    
            if ($request->hasFile('bukti_pembayaran')) {
                $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            }
    
            $pembelianAset = PembelianAsetModel::create($data);
    
            $desa = ProyekModel::where('id_proyek', $request->id_proyek)->value('desa');
            $namaAsetDasar = $desa . '_' . $request->nama_aset;
    
            $existingAsetCount = DetailPembelianAsetModel::whereHas('pembelianAset', function ($query) use ($request) {
                $query->where('id_proyek', $request->id_proyek);
            })->where('nama_aset', 'LIKE', "{$namaAsetDasar}%")
              ->count();
    
            for ($i = 1; $i <= $request->jumlah; $i++) {
                $nomorUrut = str_pad($existingAsetCount + $i, 3, '0', STR_PAD_LEFT);
                $namaAsetDetail = "{$namaAsetDasar}{$nomorUrut}";
    
                DetailPembelianAsetModel::create([
                    'id_pembelian_aset' => $pembelianAset->id_pembelian_aset,
                    'nama_aset' => $namaAsetDetail,
                    'tanggal_pembelian' => $request->tanggal,
                    'tanggal_update' => null,
                    'keterangan' => null,
                ]);
            }
    
            $jurnalBaru = new JurnalInvestasiModel();
            $jurnalBaru->id_proyek = $request->id_proyek;
            $jurnalBaru->tanggal = now()->format('Y-m-d');
            $jurnalBaru->saldo_awal = $jurnal_terakhir->saldo_akhir;
            $jurnalBaru->debit = $data['total_biaya'];
            $jurnalBaru->kredit = 0;
            $jurnalBaru->saldo_akhir = $jurnal_terakhir->saldo_akhir - $jurnalBaru->debit;
            $jurnalBaru->keterangan = 'Pembelian aset ' . $request->nama_aset . ' sebanyak ' . $request->jumlah;
            $jurnalBaru->save();
    
            Session::flash('success', 'Pembelian aset berhasil ditambahkan.');
        } else {
            Session::flash('error', 'Saldo akhir jurnal tidak mencukupi untuk pembelian aset ini.');
        }
    
        return redirect('administrator/pengelolaan/pembelian-aset');
    } 
    
    public function data_edit($id)
    {
        $title = 'Edit Pembelian Aset';
        $pembelianAset = PembelianAsetModel::findOrFail($id);
        $proyek = ProyekModel::all();
        return view('admin.pengelolaan.pembelian-aset.edit_index', compact('title', 'pembelianAset', 'proyek'));
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'masa_manfaat' => 'required|numeric',
            // 'biaya_satuan' => 'required|numeric',
            'bukti_pembayaran' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $pembelianAset = PembelianAsetModel::findOrFail($id);
        $data = $request->except('bukti_pembayaran');

        if ($request->hasFile('bukti_pembayaran')) {
            $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }

        $pembelianAset->update($data);

        if($pembelianAset){
            Session::flash('success', 'Pembelian Aset dan jurnal berhasil diperbarui.');
        }else{
            Session::flash('error', 'Saldo akhir jurnal tidak mencukupi untuk penyesuaian biaya.');
        }
        // $jurnal = JurnalInvestasiModel::where('id_proyek', $pembelianAset->id_proyek)
        //     ->where('keterangan', 'LIKE', 'Pengeluaran untuk pembelian aset: ' . $pembelianAset->nama_aset)
        //     ->first();

        // if ($jurnal) {
        //     $totalBiayaBaru = $request->jumlah * $request->biaya_satuan;
        //     $difference = $totalBiayaBaru - ($pembelianAset->jumlah * $pembelianAset->biaya_satuan);

        //     if ($jurnal->saldo_awal >= $difference) {
        //         if ($difference > 0) {
        //             $jurnal->debit += $difference;
        //             $jurnal->saldo_akhir -= $difference;
        //         } else {
        //             $jurnal->debit += $difference;
        //             $jurnal->saldo_akhir -= $difference;
        //         }
        //         $jurnal->save();

        //         Session::flash('success', 'Pembelian Aset dan jurnal berhasil diperbarui.');
        //     } else {
        //         Session::flash('error', 'Saldo akhir jurnal tidak mencukupi untuk penyesuaian biaya.');
        //     }
        // } else {
        //     Session::flash('error', 'Jurnal untuk pembelian aset ini tidak ditemukan.');
        // }

        return redirect('administrator/pengelolaan/pembelian-aset');
    }

    
    public function show($id)
    {
        $pembelianAset = PembelianAsetModel::find($id);

        if (!$pembelianAset) {
            abort(404, 'Data tidak ditemukan');
        }

        return view('admin.pengelolaan.pembelian-aset.detail_index', [
            'title' => 'Detail Pembelian Aset',
            'pembelianAset' => $pembelianAset
        ]);
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        
        $pembelianAset = PembelianAsetModel::find($id);
    
        if (!$pembelianAset) {
            Session::flash('error', 'Data tidak ditemukan');
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        DetailPembelianAsetModel::where('id_pembelian_aset', $id)->delete();
    
        $pembelianAset->delete();

        if (!$pembelianAset->exists) {
            Session::flash('success', 'Data berhasil dihapus');
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } else {
            Session::flash('error', 'Data gagal dihapus');
            return response()->json(['success' => false, 'message' => 'Data gagal dihapus']);
        }
    }
    
}
