<?php

namespace App\Http\Controllers;

use App\Models\DashboardModel;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    private $dashboardModel;

    public function __construct()
    {
        $this->dashboardModel = new DashboardModel();
    }

    public function index()
    {
        Session::put('tahun', date('Y'));
        return redirect('/administrator/dashboard');
    }

    public function dasboard()
    {
        $data = [
            'userCount' => $this->dashboardModel->getUserCount(),
            'investorCount' => $this->dashboardModel->getInvestorCount(),
            'pelangganCount' => $this->dashboardModel->getPelangganCount(),
            'proyekCount' => $this->dashboardModel->getProyekCount(),
            'proyekSelesaiCount' => $this->dashboardModel->getProyekSelesaiCount(),
            'pengajuanDesaCount' => $this->dashboardModel->getPengajuanDesaCount(),
        ];

        return view('admin.dashboard.index', compact('data'));
    }
}
