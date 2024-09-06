<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagihanModel;
use App\Models\PelangganModel;

class TagihanController extends Controller
{
    public function index()
    {
        $title = 'Tagihan Pengguna Internet';

        $tagihan = TagihanModel::join('pelanggan_models', 'tagihan_models.id_pelanggan', '=', 'pelanggan_models.id_pelanggan')
            ->select('tagihan_models.id_tagihan', 'pelanggan_models.id_pelanggan', 'pelanggan_models.nik', 'tagihan_models.tanggal', 'tagihan_models.tagihan', 'tagihan_models.metode_pembayaran', 'tagihan_models.bukti_pembayaran', 'tagihan_models.is_verified')
            ->get();

        return view('admin.pengelolaan.tagihan.index', compact('title', 'tagihan'));
    }

    public function verifikasi(Request $request, $id)
    {
        try {
            $tagihan = TagihanModel::where('id_tagihan', $id)->firstOrFail(); 
            
            if ($tagihan->is_verified == 1) {
                return response()->json(['message' => 'Tagihan sudah terverifikasi.'], 400);
            }

            $tagihan->is_verified = 1;
            $tagihan->save();

            return response()->json(['message' => 'Tagihan berhasil diverifikasi.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan.'], 500);
        }
    }
}
