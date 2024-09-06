<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProyekModel;
use App\Models\InvestasiModel;

class ProyekPageController extends Controller
{
    public function index(Request $request)
    {
        $title = 'proyek';

        $sort = $request->input('sort', 'latest'); 
        $search = $request->input('search', '');
        $filterCategory = $request->input('category', '');
        $filterLocation = $request->input('location', '');
        $filterGrade = $request->input('grade', '');

        $query = ProyekModel::query();

        if ($search) {
            $query->where('nama_proyek', 'like', "%$search%");
        }

        if ($filterCategory) {
            $query->where('status', $filterCategory);
        }

        if ($filterLocation) {
            $query->where('kabupaten', $filterLocation);
        }

        if ($filterGrade) {
            $query->where('grade', $filterGrade);
        }

        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'location':
                $query->orderBy('desa', 'asc');
                break;
            case 'value':
                $query->orderBy('roi', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $projects = $query->paginate(10);

        return view('user.proyekinvestasi', compact('title', 'projects'));
    }

    public function show($id)
    {
        $project = ProyekModel::findOrFail($id);

        $investmentCount = InvestasiModel::where('id_proyek', $id)->count();

        $totalInvestment = InvestasiModel::where('id_proyek', $id)->sum('total_investasi');

        return view('user.detailproyek', compact('project', 'investmentCount', 'totalInvestment'));
    }
}
