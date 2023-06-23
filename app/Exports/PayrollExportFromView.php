<?php

namespace App\Exports;

use App\Models\Payroll; // Mengimport model Payroll yang akan digunakan dalam kelas ini.
use Illuminate\Contracts\View\View; // Mengimport kontrak View dari framework Laravel. Digunakan sebagai tipe kembalian untuk metode view().
use Maatwebsite\Excel\Concerns\FromView; // Mengimport kontrak FromView dari pustaka Maatwebsite Excel. Diimplementasikan oleh kelas ini.
use Maatwebsite\Excel\Concerns\Exportable; // Mengimport trait Exportable dari pustaka Maatwebsite Excel. Digunakan oleh kelas ini.

class PayrollExportFromView implements FromView
{
    use Exportable;

    private $startDate; // Variabel untuk menyimpan tanggal awal.
    private $endDate; // Variabel untuk menyimpan tanggal akhir.

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate; // Menginisialisasi variabel startDate dengan nilai yang diberikan.
        $this->endDate = $endDate; // Menginisialisasi variabel endDate dengan nilai yang diberikan.
    }

    public function view(): View
    {
        $items = Payroll::with('employee')->whereBetween('date', [$this->startDate, $this->endDate])->get(); // Mengambil data gaji dengan relasi karyawan antara rentang tanggal tertentu.

        return view('report', [
            'data' => $items, // Mengirim data gaji ke view dengan nama variabel 'data'.
            'startDate' => $this->startDate, // Mengirim tanggal awal ke view dengan nama variabel 'startDate'.
            'endDate' => $this->endDate // Mengirim tanggal akhir ke view dengan nama variabel 'endDate'.
        ]);
    }
}
