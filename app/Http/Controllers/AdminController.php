<?php

namespace App\Http\Controllers;

use App\Models\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new AdminModel();
    }

    public function index()
    {
        $title = 'List User Admin';
        $admins = $this->model->get_admins();

        return view('admin.pengguna.user_admin.index', compact('title', 'admins'));
    }

    public function data_create()
    {
        $title = 'Tambahkan User Admin';

        return view('admin.pengguna.user_admin.create_index', compact('title'));
    }

    public function data_edit($id)
    {
        $title = 'Edit User Admin';
        $admin = $this->model->get_user_id($id);

        if (!$admin) {
            abort(404);
        }

        return view('admin.pengguna.user_admin.edit_index', compact('title', 'admin'));
    }

    public function create(Request $request)
    {
        $rules = [
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password_value' => 'required|string|min:6|confirmed',
            'jabatan' => 'nullable|string|max:255',
        ];

        $messages = [
            'username.required' => 'Username wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password_value.required' => 'Password baru wajib diisi',
            'password_value.min' => 'Password minimal 6 karakter',
            'password_value.confirmed' => 'Konfirmasi password baru tidak sesuai',
            'jabatan.max' => 'Jabatan maksimal 255 karakter',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'password' => Hash::make($request->password_value),
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $result = $this->model->create($data);

        if ($result) {
            Session::flash('success', 'Data berhasil disimpan');
        } else {
            Session::flash('error', 'Data gagal disimpan');
        }

        return redirect('administrator/pengguna/internal');
    }

    public function edit(Request $request)
    {
        $rules = [
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->data_id . ',id_user',
            'jabatan' => 'nullable|string|max:255',
            'current_password' => 'required|string',
        ];
    
        $messages = [
            'username.required' => 'Username wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'jabatan.max' => 'Jabatan maksimal 255 karakter',
            'current_password.required' => 'Password lama wajib diisi',
        ];
    
        if (!empty($request->new_password)) {
            $rules['new_password'] = 'required|string|min:6|confirmed';
            $messages['new_password.required'] = 'Password baru wajib diisi';
            $messages['new_password.min'] = 'Password minimal 6 karakter';
            $messages['new_password.confirmed'] = 'Konfirmasi password baru tidak sesuai';
        }
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
    
        $admin = $this->model->get_user_id($request->data_id);
    
        if (!$admin) {
            abort(404);
        }
    
        if (!Hash::check($request->current_password, $admin->password)) {
            Session::flash('error', 'Password lama tidak sesuai');
            return redirect()->back()->withInput($request->all());
        }
    
        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'updated_at' => now(),
        ];
    
        if (!empty($request->new_password)) {
            $data['password'] = Hash::make($request->new_password);
        }
    
        $result = $this->model->edit($data, $request->data_id);
    
        if ($result) {
            Session::flash('success', 'Data berhasil diperbaharui');
        } else {
            Session::flash('error', 'Data gagal diperbaharui');
        }
    
        return redirect('administrator/pengguna/internal');
    }
    
    public function verifyOldPassword(Request $request)
    {
        $admin = $this->model->get_user_id($request->data_id);

        if (!$admin || !Hash::check($request->current_password, $admin->password)) {
            return ['success' => false, 'message' => 'Password lama tidak sesuai'];
        }

        return ['success' => true];
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        return $this->model->delete_user($id);
    }
}
