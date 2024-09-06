<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanModel;
use App\Models\JurnalInvestasiModel;
use App\Models\ProyekModel;
use Illuminate\Support\Facades\Session;

class KegiatanController extends Controller
{
    public function index()
    {
        $title = 'Kegiatan Proyek Internet';
        $kegiatan = KegiatanModel::with('proyek')->get();
        return view('admin.pengelolaan.kegiatan.index', compact('title', 'kegiatan'));
    }

    public function data_create()
    {
        $title = 'Tambah Kegiatan';
        $proyek = ProyekModel::all();
        return view('admin.pengelolaan.kegiatan.create_index', compact('title', 'proyek'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'id_proyek' => 'required|exists:proyek_models,id_proyek',
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:255',
            'total_biaya' => 'required|numeric',
            'tgl_kegiatan' => 'required|date',
            'foto_nota' => 'nullable|mimes:jpg,jpeg,png,pdf,heic|max:2048',
            'foto_kegiatan' => 'nullable|mimes:jpg,jpeg,png,heic|max:2048',
        ]);

        $jurnal_terakhir = JurnalInvestasiModel::where('id_proyek', $request->id_proyek)
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($jurnal_terakhir && $jurnal_terakhir->saldo_akhir >= $request->total_biaya) {
            $data = $request->all();
            if ($request->hasFile('foto_nota')) {
                $data['foto_nota'] = $request->file('foto_nota')->store('foto_nota', 'public');
            }
            if ($request->hasFile('foto_kegiatan')) {
                $data['foto_kegiatan'] = $request->file('foto_kegiatan')->store('foto_kegiatan', 'public');
            }
    
            $result = KegiatanModel::create($data);
    
            if ($result) {
                $jurnal_baru = new JurnalInvestasiModel();
                $jurnal_baru->id_proyek = $request->id_proyek;
                $jurnal_baru->tanggal = now()->format('Y-m-d');
                $jurnal_baru->saldo_awal = $jurnal_terakhir->saldo_akhir;
                $jurnal_baru->debit = $request->total_biaya;
                $jurnal_baru->kredit = 0;
                $jurnal_baru->saldo_akhir = $jurnal_terakhir->saldo_akhir - $jurnal_baru->debit;
                $jurnal_baru->keterangan = 'Pengeluaran untuk kegiatan: ' . $request->nama_kegiatan;
                $jurnal_baru->save();
    
                Session::flash('success', 'Kegiatan berhasil ditambahkan.');
            } else {
                Session::flash('error', 'Kegiatan gagal ditambahkan.');
            }
        } else {
            Session::flash('error', 'Saldo akhir jurnal tidak mencukupi untuk pengeluaran ini.');
        }
    
        return redirect('administrator/pengelolaan/kegiatan');
    }    

    public function data_edit($id)
    {
        $title = 'Edit Kegiatan';
        $kegiatan = KegiatanModel::findOrFail($id);
        $proyek = ProyekModel::all();
        return view('admin.pengelolaan.kegiatan.edit_index', compact('title', 'kegiatan', 'proyek'));
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'id_proyek' => 'required|exists:proyek_models,id_proyek',
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|string|max:255',
            'total_biaya' => 'required|numeric',
            'tgl_kegiatan' => 'required|date',
            'foto_nota' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto_kegiatan' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
    
        $kegiatan = KegiatanModel::findOrFail($id);
        $data = $request->all();

        if ($kegiatan->total_biaya != $request->total_biaya) {
            $jurnal = JurnalInvestasiModel::where('id_proyek', $kegiatan->id_proyek)
                ->where('keterangan', 'LIKE', 'Pengeluaran untuk kegiatan: ' . $kegiatan->nama_kegiatan)
                ->first();
    
            if ($jurnal) {
                $difference = $request->total_biaya - $kegiatan->total_biaya;

                if ($jurnal->saldo_awal >= $difference) {
                    $jurnal->debit += $difference;
                    $jurnal->saldo_akhir -= $difference;
                    $jurnal->save();

                    if ($request->hasFile('foto_nota')) {
                        $data['foto_nota'] = $request->file('foto_nota')->store('foto_nota', 'public');
                    }
    
                    if ($request->hasFile('foto_kegiatan')) {
                        $data['foto_kegiatan'] = $request->file('foto_kegiatan')->store('foto_kegiatan', 'public');
                    }
    
                    $kegiatan->update($data);
                    Session::flash('success', 'Kegiatan dan jurnal berhasil diperbarui.');
                } else {
                    Session::flash('error', 'Saldo akhir jurnal tidak mencukupi untuk penyesuaian biaya.');
                }
            } else {
                Session::flash('error', 'Jurnal untuk kegiatan ini tidak ditemukan.');
            }
        } else {
            $kegiatan->update($data);
            Session::flash('success', 'Kegiatan berhasil diperbarui.');
        }
    
        return redirect('administrator/pengelolaan/kegiatan');
    }        

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $result = (new KegiatanModel)->delete_kegiatans($id);

        if ($result) {
            Session::flash('success', 'Data berhasil dihapus');
        } else {
            Session::flash('error', 'Data gagal dihapus');
        }

        return redirect('administrator/pengelolaan/kegiatan');
    }
}
