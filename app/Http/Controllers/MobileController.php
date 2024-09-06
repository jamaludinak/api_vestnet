<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MobileController extends Controller
{
    public function getHomeData(Request $request)
    {
        $user = $request->user();

        $isInvestor = DB::table('users')
            ->where('id_user', $user->id_user)
            ->value('is_verified');

        // Cek apakah user berlangganan
        $isSubscriber = DB::table('user_berlangganan_models')
            ->where('id_user', $user->id_user)
            ->exists();

        // Ambil semua id_proyek yang statusnya 'aktif'
        $activeProjectIds = DB::table('proyek_models')
            ->where('status', 'Segera hadir')
            ->pluck('id_proyek');

        // Kembalikan data dalam bentuk JSON
        return response()->json([
            'is_investor' => $isInvestor,
            'is_subscriber' => $isSubscriber,
            'active_projects' => $activeProjectIds,
        ], 200);
    }

    public function getProyekDetail($id)
    {
        // Ambil data proyek berdasarkan id_proyek langsung dari tabel 'proyek_models'
        $proyek = DB::table('proyek_models')->where('id_proyek', $id)->first();

        // Jika proyek tidak ditemukan, kembalikan respons 404
        if (!$proyek) {
            return response()->json(['error' => 'Proyek tidak ditemukan'], 404);
        }

        // Jika gambar disimpan di storage, gunakan Storage::url() untuk mengembalikan URL
        $proyek->foto_banner = Storage::url($proyek->foto_banner);

        // Kembalikan data proyek dalam bentuk JSON, termasuk path gambar asli
        return response()->json([
            'proyek' => $proyek,
        ], 200);
    }

    public function getInvestasiData(Request $request)
    {
        $user = $request->user();

        // Mengambil daftar id_investasi yang dimiliki oleh user
        $investasiIds = DB::table('investasi_models')
            ->where('id_user', $user->id_user)
            ->pluck('id_investasi');

        $mutasiTerakhir = DB::table('mutasi_models')
            ->whereIn('id_investasi', $investasiIds)
            ->orderBy('created_at', 'desc')
            ->first(); // Ambil data pertama (terbaru)

        if ($mutasiTerakhir) {
            $saldo = (float) $mutasiTerakhir->saldo_akhir;
        } else {
            // Jika tidak ada mutasi terkait, saldo default diatur ke 0
            $saldo = 0;
        }

        // Menghitung penghasilan (total kredit dengan keterangan 'bagi hasil')
        $penghasilan = DB::table('mutasi_models')
            ->whereIn('id_investasi', $investasiIds)
            ->where('keterangan', 'bagi hasil')
            ->sum(DB::raw('CAST(kredit AS DOUBLE)'));

        return response()->json([
            'saldo' => $saldo,
            'penghasilan' => $penghasilan,
        ], 200);
    }

    public function riwayatMutasi(Request $request)
    {
        $user = $request->user();

        // Mengambil daftar id_investasi yang dimiliki oleh user
        $investasiIds = DB::table('investasi_models')
            ->where('id_user', $user->id_user)
            ->pluck('id_investasi');

        // Mengambil data mutasi yang terkait dengan investasi yang dimiliki user, beserta nama desa
        $riwayatMutasi = DB::table('mutasi_models')
            ->whereIn('mutasi_models.id_investasi', $investasiIds)
            ->join('investasi_models', 'mutasi_models.id_investasi', '=', 'investasi_models.id_investasi')
            ->join('proyek_models', 'investasi_models.id_proyek', '=', 'proyek_models.id_proyek')
            ->select('mutasi_models.keterangan', 'mutasi_models.kredit', 'mutasi_models.debit', 'mutasi_models.created_at', 'proyek_models.desa')
            ->get();

        return response()->json([
            'riwayatMutasi' => $riwayatMutasi,
        ], 200);
    }



    public function getInvestDataDetail(Request $request)
    {
        $user = $request->user();

        // Mengambil total investasi pengguna
        $totalInvestasi = DB::table('investasi_models')
            ->where('id_user', $user->id_user)
            ->sum('jumlah_investasi');

        // Menghitung total penghasilan berdasarkan data yang relevan
        $penghasilan = 100000;

        // Mengambil rata-rata ROI dari semua investasi pengguna
        $roi = DB::table('investasi_models')
            ->join('proyek_models', 'investasi_models.id_proyek', '=', 'proyek_models.id_proyek')
            ->where('investasi_models.id_user', $user->id_user)
            ->avg('proyek_models.roi');

        return response()->json([
            'totalInvestasi' => $totalInvestasi,
            'penghasilan' => $penghasilan,
            'roi' => $roi,
        ], 200);
    }

    public function getProjectInvestDetail($projectId, Request $request)
    {
        $user = $request->user();

        // Ambil data proyek berdasarkan ID proyek
        $proyek = DB::table('proyek_models')
            ->where('id_proyek', $projectId)
            ->first();

        if (!$proyek) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        // Ambil total target investasi dari tabel proyek
        $totalKebutuhanDana = $proyek->target_invest;

        // Ambil jumlah investasi pengguna pada proyek ini
        $jumlahInvestasi = DB::table('investasi_models')
            ->where('id_proyek', $projectId)
            ->where('id_user', $user->id_user)
            ->sum('jumlah_investasi');

        // Menghitung persentase saham
        $presentasiSaham = 0;
        if ($totalKebutuhanDana > 0) {
            $presentasiSaham = ($jumlahInvestasi / $totalKebutuhanDana) * 100;
        }

        // Menghitung pendapatan bulanan berdasarkan ROI bulanan atau default
        $pendapatanBulanan = 100000; // Nilai default
        if ($jumlahInvestasi > 0 && $proyek->roi > 0) {
            $pendapatanBulanan = ($jumlahInvestasi * $proyek->roi / 100);
        }

        // Set total bagi hasil dengan nilai default
        $totalBagiHasil = 5000000; // Nilai default

        // Data lainnya
        $tanggalInvestasi = DB::table('investasi_models')
            ->where('id_proyek', $projectId)
            ->where('id_user', $user->id_user)
            ->value('tanggal_investasi');

        $jumlahPendukung = DB::table('investasi_models')
            ->where('id_proyek', $projectId)
            ->distinct('id_user')
            ->count('id_user');

        // Data distribusi untuk pie chart
        $distribusiDana = [
            'dividen_investor' => 50, // Contoh
            'operasional_pemeliharaan' => 30, // Contoh
            'pengembangan_proyek_lain' => 20 // Contoh
        ];

        return response()->json([
            'projectName' => $proyek->nama_proyek,
            'imageUrl' => $proyek->foto_banner,
            'location' => "{$proyek->desa}, {$proyek->kecamatan}, {$proyek->kabupaten}",
            'totalInvestasi' => $jumlahInvestasi,
            'tanggalInvestasi' => $tanggalInvestasi,
            'jumlahPendukung' => $jumlahPendukung,
            'status' => $proyek->status,
            'pendapatanBulanan' => $pendapatanBulanan,
            'presentasiSaham' => $presentasiSaham,
            'totalBagiHasil' => $totalBagiHasil,
            'distribusiDana' => $distribusiDana,
        ], 200);
    }

    public function investInProject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_proyek' => 'required|exists:proyek_models,id_proyek',
            'total_investasi' => 'required|numeric|min:100000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();

        $existingInvestment = DB::table('investasi_models')
            ->where('id_user', $user->id_user)
            ->where('id_proyek', $request->id_proyek)
            ->first();

        if ($existingInvestment) {
            return response()->json(['message' => 'You have already invested in this project.'], 400);
        }

        DB::table('investasi_models')->insert([
            'id_user' => $user->id_user,
            'id_proyek' => $request->id_proyek,
            'total_investasi' => $request->total_investasi,
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Investment successful.'], 201);
    }

    public function submitPengajuanInvestasi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'nik' => 'required|string|size:16|unique:users,nik,',
            'npwp' => 'required|string|max:20',
            'nama_bank' => 'required|string|max:50',
            'no_rekening' => 'required|string|max:20',
            'nama_pemilik_rekening' => 'required|string|max:255',
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_npwp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'no_hp' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $namaLengkap = str_replace(' ', '_', $request->nama_lengkap);
        $fotoKtpPath = $request->file('foto_ktp')->storeAs(
            'public/foto-ktp',
            "{$namaLengkap}_foto_ktp." . $request->file('foto_ktp')->getClientOriginalExtension()
        );
        $fotoNpwpPath = $request->file('foto_npwp')->storeAs(
            'public/foto-npwp',
            "{$namaLengkap}_foto_npwp." . $request->file('foto_npwp')->getClientOriginalExtension()
        );

        $user = $request->user();
        $user->update([
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'nik' => $request->nik,
            'npwp' => $request->npwp,
            'nama_bank' => $request->nama_bank,
            'no_rekening' => $request->no_rekening,
            'nama_pemilik_rekening' => $request->nama_pemilik_rekening,
            'foto_ktp' => $fotoKtpPath,
            'foto_npwp' => $fotoNpwpPath,
            'no_hp' => $request->no_hp, // Simpan no_hp ke dalam database
        ]);

        // Setelah data berhasil disimpan, panggil fungsi sendOtpWA
        return $this->sendOtpWA($request);
    }

    public function submitPengajuanInternet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_desa' => 'required|string|max:255',
            'kepala_desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'jumlah_penduduk' => 'required|integer',
            'nomor_wa' => 'required|string|max:20',
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::table('pengajuan_models')->insert([
            'nama_desa' => $request->nama_desa,
            'kepala_desa' => $request->kepala_desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'jumlah_penduduk' => $request->jumlah_penduduk,
            'nomor_wa' => $request->nomor_wa,
            'catatan' => $request->catatan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Pengajuan internet berhasil dikirim dan akan diproses.'], 201);
    }

    public function sendOtpWA(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
        ]);

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $otp_code = rand(100000, 999999);

        OtpCode::create([
            'user_id' => $user->id,
            'otp_code' => $otp_code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = Http::post('https://app.wati.io/api/v1/sendTemplateMessage', [
            'to' => $user->no_hp,
            'template_name' => 'otp_template',
            'parameters' => [
                ['type' => 'text', 'text' => $otp_code]
            ],
            'key' => env('WATI_API_KEY')
        ]);

        if ($response->successful()) {
            return response()->json(['message' => 'OTP sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send OTP.'], 500);
        }
    }

    public function validateOtpWA(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'otp_code' => 'required|string',
        ]);

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $otp_record = OtpCode::where('user_id', $user->id)
            ->where('otp_code', $request->otp_code)
            ->where('expires_at', '>', now())
            ->first();

        if ($otp_record) {
            $otp_record->update(['verified_at' => now()]);

            $user->update(['no_hp_verified_at' => now()]);

            return response()->json(['message' => 'OTP validated and phone number verified successfully.'], 200);
        } else {
            return response()->json(['error' => 'Invalid OTP or OTP expired.'], 400);
        }
    }

    public function resendOtpWA(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
        ]);

        $user = User::where('no_hp', $request->no_hp)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $otp_code = rand(100000, 999999);

        OtpCode::create([
            'user_id' => $user->id,
            'otp_code' => $otp_code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = Http::post('https://app.wati.io/api/v1/sendTemplateMessage', [
            'to' => $user->no_hp,
            'template_name' => 'otp_template',
            'parameters' => [
                ['type' => 'text', 'text' => $otp_code]
            ],
            'key' => env('WATI_API_KEY')
        ]);

        if ($response->successful()) {
            return response()->json(['message' => 'OTP sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to send OTP.'], 500);
        }
    }

    public function getUserProfile(Request $request)
    {
        $user = $request->user();

        $data = [
            'username' => $user->username,
            'nama_lengkap' => $user->nama_lengkap,
            'tempat_lahir' => $user->tempat_lahir,
            'tanggal_lahir' => $user->tgl_lahir,
            'email' => $user->email,
            'no_hp' => $user->no_hp,
            'nik' => $user->nik,
            'npwp' => $user->npwp,
            'nama_bank' => $user->nama_bank,
            'nomor_rekening' => $user->nomor_rekening,
            'nama_pemilik_rekening' => $user->nama_pemilik_rekening,
        ];

        return response()->json($data, 200);
    }

    public function updateUserProfile(Request $request)
    {
        $request->validate([
            'username' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8',
            'no_hp' => 'sometimes|string|max:15',
        ]);

        $user = $request->user();

        if ($request->has('username')) {
            $user->username = $request->username;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('no_hp')) {
            $user->no_hp = $request->no_hp;
        }

        $user->save();

        return response()->json(['message' => 'Profile updated successfully'], 200);
    }

    public function getAllProyekDetails()
    {
        try {
            $proyek = DB::table('proyek_models')
                ->select('id_proyek', 'kabupaten', 'desa')
                ->get();
            return response()->json($proyek, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve project details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getActiveProjects(Request $request)
    {
        try {
            $activeProjectIds = DB::table('proyek_models')
                ->where('status', 'Segera hadir')
                ->select('id_proyek')
                ->get();

            return response()->json($activeProjectIds, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch active project ids'], 500);
        }
    }


    public function getUserInvestedProjects(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Query the 'investasi_models' table to get all project IDs the user has invested in
        $investedProjects = DB::table('investasi_models')
            ->where('id_user', $user->id_user)
            ->pluck('id_proyek'); // Assuming 'id_proyek' is the column for the project ID in the 'investasi_models' table

        // Return the project IDs as a JSON response
        return response()->json(['invested_project_ids' => $investedProjects], 200);
    }
}
