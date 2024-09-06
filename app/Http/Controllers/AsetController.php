<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPembelianAsetModel;
use Carbon\Carbon;

class AsetController extends Controller
{
    public function index()
    {
        $title = 'Daftar Aset';
        $aset = DetailPembelianAsetModel::with('pembelianAset')->get();
    
        return view('admin.pengelolaan.aset.index', compact('title', 'aset'));
    } 
    
    public function edit($id)
    {
        $title = 'Edit Aset';
        $aset = DetailPembelianAsetModel::findOrFail($id);
        
        return view('admin.pengelolaan.aset.edit_index', compact('title', 'aset'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_update' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $aset = DetailPembelianAsetModel::findOrFail($id);
        $aset->tanggal_update = $request->tanggal_update;
        $aset->keterangan = $request->keterangan;
        $aset->save();

        return redirect('administrator/pengelolaan/asset')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $aset = DetailPembelianAsetModel::find($id);
    
        if ($aset) {
            $result = $aset->delete();
            
            if ($result) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data gagal dihapus'
                ]);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }    
}
