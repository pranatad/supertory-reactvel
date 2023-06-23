<?php

namespace App\Http\Controllers;

use App\Exports\PayrollExport;
use App\Exports\PayrollExportFromView;
use App\Models\Payroll;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Membuat query untuk mengambil data payroll dengan relasi employee
        $query = Payroll::with('employee');

        // Mengatur tanggal awal dan akhir bulan saat ini
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        // Jika terdapat tanggal awal dan akhir yang diberikan dalam permintaan, mengubah query dan mengatur tanggal awal dan akhir sesuai permintaan
        if ($request->startDate != null && $request->endDate != null) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
            $startDate = $request->startDate;
            $endDate = $request->endDate;
        } else {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        // Mengembalikan respons menggunakan template Inertia dengan data payroll
        return inertia('Report', [
            'payrolls' => $query->orderBy('date', 'desc')->paginate(10),
            '_startDate' => $startDate,
            '_endDate' => $endDate
        ]);
    }

    public function export(Request $request)
    {
        // Menghasilkan file eksport menggunakan kelas PayrollExport dengan tanggal awal dan akhir yang diberikan dan mengunduhnya dalam format XLSX
        return (new PayrollExport($request->startDate, $request->endDate))->download('reports.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportPdf(Request $request)
    {
        // Menghasilkan file PDF menggunakan kelas PayrollExportFromView dengan tanggal awal dan akhir yang diberikan dan mengunduhnya dalam format PDF
        return (new PayrollExportFromView($request->startDate, $request->endDate))->download('reports.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }
}
