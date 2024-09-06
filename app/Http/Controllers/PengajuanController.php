<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanModel;
use Illuminate\Support\Facades\Session;

class PengajuanController extends Controller
{
    public function index()
    {
        $title = 'Pengajuan Internet Desa';
        $pengajuan = PengajuanModel::all();
        return view('admin.pengajuan-desa.index', compact('title', 'pengajuan'));
    }

    public function store(Request $request)
    {
        // Validasi data
        $validatedData = $request->validate([
            'nama_desa' => 'required|string|max:255',
            'kepala_desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'jumlah_penduduk' => 'required|integer',
            'nomor_wa' => 'required|string|max:15',
            'catatan' => 'nullable|string',
        ]);

        try {
            // Simpan data ke database
            PengajuanModel::create($validatedData);

            // Redirect dengan pesan sukses
            return redirect('/home')->with('success', 'Pengajuan berhasil dikirim.');
        } catch (\Exception $e) {
            // Redirect dengan pesan error
            return redirect()->back()->with('error', 'Pengajuan gagal, silahkan coba lagi.');
        }
    }

    public function show($id)
    {
        $title = 'Detail Pengajuan Internet Desa';
        $pengajuan = PengajuanModel::find($id);
        return view('admin.pengajuan-desa.detail_index', compact('title', 'pengajuan'));
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $result = PengajuanModel::delete_pengajuan($id);

        if ($result) {
            Session::flash('success', 'Data berhasil dihapus');
        } else {
            Session::flash('error', 'Data gagal dihapus');
        }

        return redirect('administrator/pengajuan-desa');
    }
}
