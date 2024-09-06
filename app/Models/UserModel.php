<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{
    use HasFactory;

    protected $table = 'users';

    public function get_user($request)
    {
        return DB::table($this->table)
            ->where('role_id', '!=', 1)
            ->get();
    }

    public function get_user_by_id($id_user)
    {
        return DB::table($this->table)
            ->where('id_user', $id_user)
            ->first();
    }

    public function verifikasi_investor($id_user)
    {
        return DB::table($this->table)
            ->where('id_user', $id_user)
            ->update(['is_verified' => 1]);
    }

    public function delete_user($id)
    {
        return DB::table($this->table)
            ->where('id_user', $id)
            ->where('role_id', '!=', 1)
            ->delete();
    }
}
