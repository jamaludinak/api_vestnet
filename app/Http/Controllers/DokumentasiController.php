<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumentasiModel;
use App\Models\ProyekModel;

class DokumentasiController extends Controller
{
    public function index()
    {
        $title = 'Daftar Dokumentasi Proyek';
        $dokumentasi = DokumentasiModel::with('proyek')->get();

        return view('admin.proyek-investasi.dokumentasi-proyek.index', compact('title', 'dokumentasi'));
    }

    public function create()
    {
        $title = 'Tambah Dokumentasi Proyek';
        $proyek = ProyekModel::all();

        return view('admin.proyek-investasi.dokumentasi-proyek.create_index', compact('title', 'proyek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_proyek' => 'required|exists:proyek_models,id_proyek',
            'file_url.*' => 'required|file|mimes:jpg,jpeg,png,heic|max:2048',
        ]);

        $files = $request->file('file_url');

        foreach ($files as $file) {
            $path = $file->store('dokumentasi', 'public');

            DokumentasiModel::create([
                'id_proyek' => $request->input('id_proyek'),
                'file_url' => $path,
            ]);
        }

        return redirect('administrator/proyek-investasi/dokumentasi')->with('success', 'Dokumentasi berhasil ditambahkan');
    }

    public function deleteDokumentasai(Request $request)
    {
        $id = $request->input('id');
    
        try {
            $result = DokumentasiModel::where('id_dokumentasi', $id)->delete();
        
            if ($result) {
                return response()->json(['success' => 'Dokumentasi berhasil dihapus']);
            } else {
                return response()->json(['error' => 'Dokumentasi gagal dihapus'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat menghapus dokumentasi: ' . $e->getMessage()], 500);
        }
    }    
}
