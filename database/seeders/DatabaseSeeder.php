<?php

namespace Database\Seeders;

use App\Models\PelangganModel;
use App\Models\User;
use App\Models\InvestasiModel;
use App\Models\JenisPaketModel;
use App\Models\TagihanModel;
use App\Models\PenarikanSaldoUserModel;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //     User::create([
    //         'username' => 'Tanzil',
    //         'jabatan' => 'Admin',
    //         'email' => 'tanzil@vestnet.id',
    //         'password' => Hash::make('admin123'),
    //         'role_id' => '1'
    //     ]);
    // }

    public function run(): void
    {
        User::create([
            'username' => 'Tanzil',
            'jabatan' => 'Admin',
            'email' => 'tanzil@vestnet.id',
            'password' => Hash::make('tanzil123'),
            'role_id' => '1'
        ]);

        // $filePath = public_path('assets/img/all-img/about-image.png'); // Ganti 'your-file-name.jpg' dengan nama file Anda

        // Simpan file bukti transfer ke dalam storage dan dapatkan path-nya
        // $fotoKtp = Storage::disk('public')->put('foto_ktp', new \Illuminate\Http\File($filePath));
        // $filePath2 = public_path('assets/img/all-img/about-image.png'); // Ganti 'your-file-name.jpg' dengan nama file Anda

        // // Simpan file bukti transfer ke dalam storage dan dapatkan path-nya
        // $fotoNpwp = Storage::disk('public')->put('foto_npwp', new \Illuminate\Http\File($filePath2));
        // User::create([
        //     'username' => 'oke',
        //     'email' => 'oke@xman.id',
        //     'password' => Hash::make('oke12345'),
        //     'nama_lengkap' => 'Hugh Jackman',
        //     'no_hp' => '083861673722',
        //     'foto_ktp' => $fotoKtp,
        //     'foto_npwp' => $fotoNpwp,
        // ]);

        // PelangganModel::create([
        //     'id_user' => '10',
        // ]);

        // $filePath = public_path('assets/img/all-img/about-image.png'); // Ganti 'your-file-name.jpg' dengan nama file Anda

        // // Simpan file bukti transfer ke dalam storage dan dapatkan path-nya
        // $buktiTransferPath = Storage::disk('public')->put('bukti_transfer', new \Illuminate\Http\File($filePath));

        // InvestasiModel::create([
        //     'id_user' => 4,
        //     'id_proyek' => 3,
        //     'total_investasi' => 2000000,
        //     'tanggal' => Carbon::now(),
        //     'bukti_transfer' => $buktiTransferPath,
        // ]);

        // $filePath = public_path('assets/img/all-img/about-image.png'); // Ganti 'your-file-name.jpg' dengan nama file Anda

        // // Simpan file bukti transfer ke dalam storage dan dapatkan path-nya
        // $buktiPembayaran = Storage::disk('public')->put('bukti_transfer', new \Illuminate\Http\File($filePath));

        // TagihanModel::create([
        //     'id_pelanggan' => 'MAR010',          
        //     'tanggal' => Carbon::now(),
        //     'tagihan' => 120000,
        // ]);

        // PenarikanSaldoUserModel::create([
        //     'id_investasi' => 24,          
        //     'tanggal_pengajuan' => Carbon::now(),
        //     'jumlah' => 500000,
        // ]);

        // $filePath = public_path('assets/img/all-img/about-image.png'); // Ganti 'your-file-name.jpg' dengan nama file Anda

        // // Simpan file bukti transfer ke dalam storage dan dapatkan path-nya
        // $foto_ktp = Storage::disk('public')->put('foto_ktp', new \Illuminate\Http\File($filePath));
        // JenisPaketModel::create([
        //     'id_proyek' => 2,
        //     'nama' => 'Paket B',
        //     'deskripsi' => 'jdkajsdjakljsdlkad',
        //     'harga_berlangganan' => 300000,
        // ]);

        // $filePath = public_path('assets/img/all-img/about-image.png'); // Ganti 'your-file-name.jpg' dengan nama file Anda

        // // Simpan file bukti transfer ke dalam storage dan dapatkan path-nya
        // $buktiTransferPath = Storage::disk('public')->put('bukti_transfer', new \Illuminate\Http\File($filePath));
        // TagihanModel::create([
        //     'id_pelanggan' => 'MAR010',
        //     'tanggal' => Carbon::now(),
        //     'tagihan' => 300000,
        //     'metode_pembayaran' => 'Transfer',
        //     'bukti_pembayaran' => $buktiTransferPath,
        // ]);
    }
}
