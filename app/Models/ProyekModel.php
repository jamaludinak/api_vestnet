<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProyekModel extends Model
{
    use HasFactory;

    protected $table = 'proyek_models';
    protected $primaryKey = 'id_proyek';
    public $incrementing = true;
    public $timestamps = true;

    public function get_all_projects()
    {
        return DB::table($this->table)->get();
    }

    public function get_project_by_id($id)
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->first();
    }

    public function create($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function edit($data, $id)
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->update($data);
    }

    public function delete_project($id)
    {
        return DB::table($this->table)
            ->where($this->primaryKey, $id)
            ->delete();
    }
    
    public function dokumentasi()
    {
        return $this->hasMany(DokumentasiModel::class, 'id_proyek', 'id_proyek');
    }
}
