<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PelangganModel;
use App\Models\JenisPaketModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PelangganController extends Controller
{
    public function index()
    {
        $title = 'List Pelanggan Internet';
        $pelanggan = PelangganModel::join('jenis_paket_models', 'pelanggan_models.id_paket', '=', 'jenis_paket_models.id_paket')
            ->select('pelanggan_models.*', 'jenis_paket_models.nama')
            ->get();

        foreach ($pelanggan as $p) {
            $p->status = User::where('nik', $p->nik)->exists() ? 'user' : 'non user';
        }

        return view('admin.pengelolaan.pelanggan-internet.index', compact('title', 'pelanggan'));
    }

    public function create()
    {
        $title = 'Tambah Pelanggan Internet';
        $jenis_paket = JenisPaketModel::all();
        return view('admin.pengelolaan.pelanggan-internet.create_index', compact('title', 'jenis_paket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_paket' => 'required|exists:jenis_paket_models,id_paket',
            'tanggal_pemasangan' => 'required|date',
            'biaya_berlangganan' => 'required|numeric',
            'biaya_pemasangan' => 'required|numeric',
            'nik' => 'required|string|max:16',
            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png,heic|max:2048',
        ]);

        $fotoKtpPath = $request->file('foto_ktp')->store('ktp', 'public');

        $pelanggan = PelangganModel::create([
            'id_paket' => $request->id_paket,
            'tanggal_pemasangan' => $request->tanggal_pemasangan,
            'biaya_berlangganan' => $request->biaya_berlangganan,
            'biaya_pemasangan' => $request->biaya_pemasangan,
            'nik' => $request->nik,
            'foto_ktp' => $fotoKtpPath,
        ]);

        // Generate id_pelanggan
        $jenisPaket = JenisPaketModel::find($request->id_paket);
        $proyek = $jenisPaket->proyek;
        $lastWord = explode(' ', trim($proyek->nama_proyek));
        $prefix = strtoupper(substr(end($lastWord), 0, 3));
        $id_pelanggan = $prefix . str_pad($pelanggan->id_pelanggan_base, 3, '0', STR_PAD_LEFT);
        $pelanggan->update(['id_pelanggan' => $id_pelanggan]);

        return redirect('administrator/pengelolaan/pelanggan')->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function edit($id_pelanggan)
    {
        $title = 'Edit Pelanggan Internet';
        $pelanggan = PelangganModel::findOrFail($id_pelanggan);
        $jenis_paket = JenisPaketModel::all();
        return view('admin.pengelolaan.pelanggan-internet.edit_index', compact('title', 'pelanggan', 'jenis_paket'));
    }

    public function update(Request $request, $id_pelanggan)
    {
        $request->validate([
            'id_paket' => 'required|exists:jenis_paket_models,id_paket',
            'tanggal_pemasangan' => 'required|date',
            'biaya_berlangganan' => 'required|numeric',
            'biaya_pemasangan' => 'required|numeric',
            'nik' => 'required|string|max:16',
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $pelanggan = PelangganModel::findOrFail($id_pelanggan);
        $pelanggan->update([
            'id_paket' => $request->id_paket,
            'tanggal_pemasangan' => $request->tanggal_pemasangan,
            'biaya_berlangganan' => $request->biaya_berlangganan,
            'biaya_pemasangan' => $request->biaya_pemasangan,
            'nik' => $request->nik,
            'foto_ktp' => $request->hasFile('foto_ktp') ? $request->file('foto_ktp')->store('ktp', 'public') : $pelanggan->foto_ktp,
        ]);

        return redirect('administrator/pengelolaan/pelanggan')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $result = (new PelangganModel)->delete_pelanggan($id);

        if ($result) {
            Session::flash('success', 'Data berhasil dihapus');
        } else {
            Session::flash('error', 'Data gagal dihapus');
        }

        return redirect('administrator/pengelolaan/pelanggan');
    }
}
