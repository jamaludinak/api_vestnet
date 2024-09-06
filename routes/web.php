<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumentasiController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\TentangKamiPageController;
use App\Http\Controllers\SimulasiKeuntunganPageController;
use App\Http\Controllers\EdukasiPageController;
use App\Http\Controllers\DownloadPageController;
use App\Http\Controllers\InvestasiController;
use App\Http\Controllers\JurnalInvestasiController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PenarikanSaldoUserController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\ProyekPageController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\UserController;


//User
Route::get('/', [HomePageController::class, 'index']);

Route::prefix('home')->group(function () {
    Route::get('/',                  [HomePageController::class, 'index']);
    Route::post('/pengajuan',        [PengajuanController::class, 'store']);
});

Route::prefix('proyek-investasi')->group(function () { 
    Route::get('/',                  [ProyekPageController::class, 'index'])->name('proyek.investasi');
    Route::get('/detail/{id}',       [ProyekPageController::class, 'show'])->name('proyek.detail');
});

Route::prefix('tentang-kami')->group(function () {
    Route::get('/',                  [TentangKamiPageController::class, 'index']);
});

Route::prefix('simulasi-keuntungan')->group(function () {
    Route::get('/',                  [SimulasiKeuntunganPageController::class, 'index']);
});

Route::prefix('edukasi')->group(function () {
    Route::get('/',                     [EdukasiPageController::class, 'index']);
    Route::get('/dasar-investasi',      [EdukasiPageController::class, 'index']);
    Route::get('/manajemen-resiko',     [EdukasiPageController::class, 'index2']);
    Route::get('/strategi-investasi',   [EdukasiPageController::class, 'index3']);
});

Route::prefix('download')->group(function () {
    Route::get('/',                  [DownloadPageController::class, 'index']);
});

//Administrator
Route::prefix('administrator')->group(function () {
    Route::get('/',                   [AuthController::class, 'index'])->name('login');;
    Route::get('/login',              [AuthController::class, 'index'])->name('login');
    Route::post('/login-attempt',     [AuthController::class, 'login_attempt']);
    Route::post('/logout',            [AuthController::class, 'logout']);

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/',               [DashboardController::class, 'index']);
        Route::get('dashboard',       [DashboardController::class, 'dasboard']);
    });

    //Pengguna
        Route::prefix('pengguna')->group(function () {
        // User Admin
            Route::prefix('internal')->group(function () {
                Route::get('/',                     [AdminController::class, 'index']);
                Route::post('data-get',             [AdminController::class, 'get_data']);
                Route::get('data-create',           [AdminController::class, 'data_create']);
                Route::get('data-edit/{id}',        [AdminController::class, 'data_edit']);
                Route::post('data-save',            [AdminController::class, 'create']);
                Route::post('data-edit',            [AdminController::class, 'edit']);
                Route::post('data-delete',          [AdminController::class, 'delete']);
            });
                //User
                Route::prefix('user')->group(function () {
                Route::get('/',                         [UserController::class, 'index']);
                Route::get('/detail/{id_user}',         [UserController::class, 'detail']);
                Route::post('data-delete',              [UserController::class, 'delete']);
            });
        });
        //Investasi Internet
        Route::prefix('investasi-internet')->group(function () {
            //Pengajuan Investor
            Route::prefix('pengajuan-investor')->group(function () {
                Route::get('/',                             [UserController::class, 'pengajuanInvestor']);
                Route::get('/verifikasi/{id_user}',         [UserController::class, 'verifikasi']);
                Route::post('/verifikasi/update/{id_user}', [UserController::class, 'verifikasiInvestor']);
            });
            //Data Investasi
            Route::prefix('data-investasi')->group(function () {
                Route::get('/',                 [InvestasiController::class, 'index']);
                Route::get('/tinjau/{id}',      [InvestasiController::class, 'tinjau']);
                Route::post('/setujui/{id}',    [InvestasiController::class, 'approve']);
                Route::post('/tolak/{id}',      [InvestasiController::class, 'reject']);
            });
            //Mutasi Investor
            Route::prefix('mutasi-investor')->group(function () {
                Route::get('/',                 [MutasiController::class, 'index']);
                Route::get('/detail/{id}',      [MutasiController::class, 'show']);
            });
            //Penarikan Saldo
            Route::prefix('penarikan-saldo')->group(function () {
                Route::get('/',                 [PenarikanSaldoUserController::class, 'index']);
                Route::get('/tinjau/{id}',      [PenarikanSaldoUserController::class, 'peninjauan']);
                Route::post('/setujui/{id}',    [PenarikanSaldoUserController::class, 'approve']);
                Route::post('/tolak/{id}',      [PenarikanSaldoUserController::class, 'reject']);
            });
        });

        //Proyek Investasi
        Route::prefix('proyek-investasi')->group(function () {
            //Data Proyek
            Route::prefix('data-proyek')->group(function () {
                Route::get('/',                     [ProyekController::class, 'index']);
                Route::get('/data-detail/{id}',     [ProyekController::class, 'show']);
                Route::get('/data-create',          [ProyekController::class, 'data_create']);
                Route::get('/data-edit/{id}',       [ProyekController::class, 'edit']);
                Route::post('/data-save',           [ProyekController::class, 'create']);
                Route::put('/update/{id}',          [ProyekController::class, 'update']);
                Route::post('/data-delete',         [ProyekController::class, 'delete']);
                Route::post('/dokumentasi/delete',  [ProyekController::class, 'deleteDokumentasi']);
            });
            //Dokumentasi Proyek
            Route::prefix('dokumentasi')->group(function () {
                Route::get('/',                 [DokumentasiController::class, 'index']);
                Route::get('/data-create',      [DokumentasiController::class, 'create']);
                Route::post('/data-save',       [DokumentasiController::class, 'store']);
                Route::post('/delete',          [DokumentasiController::class, 'deleteDokumentasai']);
            });
            //Jurnal Proyek
            Route::prefix('jurnal')->group(function () {
                Route::get('/',                 [JurnalInvestasiController::class, 'index']);
                Route::get('/detail/{id}',      [JurnalInvestasiController::class, 'show']);
            });
        });

        //Pengelolaan
        Route::prefix('pengelolaan')->group(function () {
            //Pelanggan
            Route::prefix('pelanggan')->group(function () {
                Route::get('/',                 [PelangganController::class, 'index']);
                Route::get('data-create',       [PelangganController::class, 'create']);
                Route::get('data-edit/{id}',    [PelangganController::class, 'edit']);
                Route::post('data-save',        [PelangganController::class, 'store']);
                Route::put('data-update/{id}',  [PelangganController::class, 'update']);
                Route::post('data-delete',      [PelangganController::class, 'delete']);
            });
            //Kegiatan
            Route::prefix('kegiatan')->group(function () {
                Route::get('/',                     [KegiatanController::class, 'index']);
                Route::get('data-create',           [KegiatanController::class, 'data_create']);
                Route::get('data-edit/{id}',        [KegiatanController::class, 'data_edit']);
                Route::post('data-save',            [KegiatanController::class, 'create']);
                Route::put('data-update/{id}',      [KegiatanController::class, 'edit']);
                Route::post('data-delete',          [KegiatanController::class, 'delete']);
            });
            //Tagihan
            Route::prefix('tagihan')->group(function () {
                Route::get('/',                 [TagihanController::class, 'index']);
                Route::post('/verifikasi/{id}', [TagihanController::class, 'verifikasi']);
            });
        });
    //Pengajuan Desa
    Route::prefix('pengajuan-desa')->group(function () {
        Route::get('/',                 [PengajuanController::class, 'index']);
        Route::get('/data-detail/{id}', [PengajuanController::class, 'show']);
        Route::post('/data-delete',     [PengajuanController::class, 'delete']);
    });
});