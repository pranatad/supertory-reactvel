<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Employee;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        // Mendapatkan tanggal awal dan akhir minggu saat ini
        $now = now();
        $startDate = $now->startOfWeek()->format('Y-m-d');
        $endDate = $now->endOfWeek()->format('Y-m-d');

        // Jika terdapat tanggal awal dan akhir yang diberikan melalui permintaan, gunakan tanggal tersebut
        if ($request->startDate && $request->endDate) {
            $startDate = Carbon::parse($request->startDate)->format('Y-m-d');
            $endDate = Carbon::parse($request->endDate)->format('Y-m-d');
        }

        // Menghitung total jumlah gaji yang diterima dan total jumlah item pada periode tanggal yang diberikan
        $totalAmount = Payroll::whereBetween('date', [$startDate, $endDate])->sum('recived');
        $totalItem = Payroll::whereBetween('date', [$startDate, $endDate])->sum('item_count');

        // Mengambil data untuk grafik dengan mengelompokkan gaji berdasarkan tanggal
        $charts = Payroll::selectRaw('SUM(recived) as amount, date')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->groupBy('date')
            ->get();

        // Mengambil data karyawan dengan menghitung total gaji dan jumlah item yang diterima oleh masing-masing karyawan
        $employees = Payroll::selectRaw('employee_id, SUM(recived) as amount, SUM(item_count) as count')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('employee_id')
            ->with(['employee'])
            ->get();

        // Mengambil data produk dengan menghitung total jumlah produk yang terjual
        $products = PayrollItem::selectRaw('product_id, SUM(quantity) as count')
            ->whereHas('payroll', function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->with(['product'])
            ->get();

        // Mengembalikan respons inertia dengan data yang dibutuhkan untuk tampilan dashboard
        return inertia('Dashboard', [
            'product' => Product::count(), // Jumlah produk
            'employee' => Employee::count(), // Jumlah karyawan
            'totalAmount' => $totalAmount, // Total jumlah gaji yang diterima
            'totalItem' => $totalItem, // Total jumlah item
            'charts' => $charts, // Data untuk grafik
            'employees' => $employees, // Data karyawan
            'products' => $products, // Data produk
            '_startDate' => $startDate, // Tanggal awal
            '_endDate' => $endDate // Tanggal akhir
        ]);
    }
}
