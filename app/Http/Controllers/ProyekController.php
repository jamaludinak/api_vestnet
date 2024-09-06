<?php

namespace App\Http\Controllers;

use App\Models\ProyekModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class ProyekController extends Controller
{
    public function index()
    {
        $title = 'Proyek Investasi';
        $projects = (new ProyekModel)->get_all_projects();
        return view('admin.proyek-investasi.data-proyek.index', compact('title', 'projects'));
    }

    public function data_create()
    {
        $title = 'Tambahkan Proyek';
        return view('admin.proyek-investasi.data-proyek.create_index', compact('title'));
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_proyek' => 'required|string|max:255',
            'url_map' => 'required|string',
            'foto_banner' => 'required|image|mimes:jpeg,png,jpg,heic|max:2048',
            'deskripsi' => 'required|string',
            'swot' => 'sometimes|required|string',
            'simulasi_keuntungan' => 'sometimes|required|string',
            'min_invest' => 'required|numeric',
            'target_invest' => 'required|numeric',
            'desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|in:Cilacap,Banjarnegara,Banyumas,Purbalingga',
            'roi' => 'required|numeric',
            'bep' => 'required|numeric',
            'grade' => 'required|string|in:A,B,C,D,E',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $fotoPath = $request->file('foto_banner')->store('proyek_foto', 'public');

        $result = (new ProyekModel)->create([
            'nama_proyek' => $request->nama_proyek,
            'url_map' => $request->url_map,
            'foto_banner' => $fotoPath,
            'deskripsi' => $request->deskripsi,
            'swot' => $request->swot,
            'simulasi_keuntungan' => $request->simulasi_keuntungan,
            'min_invest' => $request->min_invest,
            'dana_terkumpul' => 0,
            'target_invest' => $request->target_invest,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'roi' => $request->roi,
            'bep' => $request->bep,
            'grade' => $request->grade,
            'status' => 'Segera hadir',
            'created_at' => now(),
        ]);

        if ($result) {
            Session::flash('success', 'Data berhasil disimpan');
        } else {
            Session::flash('error', 'Data gagal disimpan');
        }

        return redirect('administrator/proyek-investasi/data-proyek');
    }

    public function edit($id)
    {
        $title = 'Edit Proyek';
        $project = (new ProyekModel)->get_project_by_id($id);
        return view('admin.proyek-investasi.data-proyek.edit_index', compact('title', 'project'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_proyek' => 'sometimes|required|string|max:255',
            'url_map' => 'sometimes|required|string|max:2048',
            'foto_banner' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'sometimes|required|string',
            'swot' => 'sometimes|required|string',
            'simulasi_keuntungan' => 'sometimes|required|string',
            'min_invest' => 'sometimes|required|numeric',
            'target_invest' => 'sometimes|required|numeric',
            'desa' => 'sometimes|required|string|max:255',
            'kecamatan' => 'sometimes|required|string|max:255',
            'kabupaten' => 'sometimes|required|string|in:Cilacap,Banjarnegara,Banyumas,Purbalingga',
            'roi' => 'sometimes|required|numeric',
            'bep' => 'sometimes|required|numeric',
            'grade' => 'sometimes|required|string|in:A,B,C,D,E',
            'status' => 'sometimes|required|string|in:Segera hadir,Dalam pendanaan'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Ambil data yang ingin diperbarui
        $data = $request->only([
            'nama_proyek',
            'url_map',
            'deskripsi',
            'swot',
            'simulasi_keuntungan',
            'min_invest',
            'target_invest',
            'desa',
            'kecamatan',
            'kabupaten',
            'roi',
            'bep',
            'grade',
            'status'
        ]);
    
        // Periksa apakah ada file foto_banner yang diunggah
        if ($request->hasFile('foto_banner')) {
            // Hapus foto lama
            $existingProject = ProyekModel::find($id);
            if ($existingProject && $existingProject->foto_banner) {
                Storage::disk('public')->delete($existingProject->foto_banner);
            }
    
            // Unggah foto baru
            $fotoPath = $request->file('foto_banner')->store('proyek_foto', 'public');
            $data['foto_banner'] = $fotoPath;
        }
    
        // Update data proyek
        $result = ProyekModel::where('id_proyek', $id)->update($data);
    
        if ($result) {
            Session::flash('success', 'Data berhasil diperbarui');
        } else {
            Session::flash('error', 'Data gagal diperbarui');
        }
    
        return redirect('administrator/proyek-investasi/data-proyek');
    }
            
    
    public function show($id)
    {
        $title = 'Detail Proyek';
        $project = (new ProyekModel)->get_project_by_id($id);
        $dokumentasi = DokumentasiModel::where('id_proyek', $id)->get(); 
        return view('admin.proyek-investasi.data-proyek.lihat_selengkapnya_index', compact('title', 'project', 'dokumentasi'));
    }
    

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $result = (new ProyekModel)->delete_project($id);

        if ($result) {
            Session::flash('success', 'Data berhasil dihapus');
        } else {
            Session::flash('error', 'Data gagal dihapus');
        }

        return redirect('administrator/proyek-investasi/data-proyek');
    }
}
