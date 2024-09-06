<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminModel extends Model
{
    use HasFactory;

    protected $table = 'users';

    public function get_admins()
    {
        return DB::table($this->table)
            ->where('role_id', 1)
            ->get();
    }

    public function get_user_id($id)
    {
        return DB::table($this->table)
            ->where('id_user', $id)
            ->where('role_id', 1)
            ->first();
    }

    public function create($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function edit($data, $id)
    {
        return DB::table($this->table)
            ->where('id_user', $id)
            ->where('role_id', 1)
            ->update($data);
    }

    public function delete_user($id)
    {
        return DB::table($this->table)
            ->where('id_user', $id)
            ->where('role_id', 1)
            ->delete();
    }
}
