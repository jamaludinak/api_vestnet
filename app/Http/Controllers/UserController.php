<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index(Request $request)
    {
        $title = 'List User';
        $users = $this->model->get_user($request);
        return view('admin.pengguna.user.index', compact('title', 'users'));
    }

    public function get_data(Request $request)
    {
        $users = $this->model->get_user($request);
        return response()->json($users);
    }

    public function detail($id_user)
    {
        $title = 'Detail User';
        $user = $this->model->get_user_by_id($id_user);
        return view('admin.pengguna.user.detail_index', compact('title', 'user'));
    }

    public function pengajuanInvestor(Request $request)
    {
        $title = 'Pengajuan Investor';
        $users = $this->model->get_user($request);
        return view('admin.investasi-internet.pengajuan-investor.index', compact('title', 'users'));
    }

    public function verifikasi($id_user)
    {
        $title = 'Verifikasi Pengajuan Investor';
        $user = $this->model->get_user_by_id($id_user);
        return view('admin.investasi-internet.pengajuan-investor.verifikasi_index', compact('title', 'user'));
    }

    public function verifikasiInvestor(Request $request, $id_user)
    {
        $this->model->verifikasi_investor($id_user);
        return redirect('admin/investasi-internet/pengajuan-investor')->with('success', 'User berhasil diverifikasi sebagai investor.');
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        return $this->model->delete_user($id);
    }
}
