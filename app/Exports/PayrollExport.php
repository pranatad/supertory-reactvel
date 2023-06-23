<?php

namespace App\Exports;

use App\Models\Payroll; // Mengimport model Payroll yang akan digunakan dalam kelas ini.
use Maatwebsite\Excel\Concerns\FromCollection; // Mengimport kontrak FromCollection dari pustaka Maatwebsite Excel. Diimplementasikan oleh kelas ini.
use Maatwebsite\Excel\Concerns\Exportable; // Mengimport trait Exportable dari pustaka Maatwebsite Excel. Digunakan oleh kelas ini.

class PayrollExport implements FromCollection
{
    use Exportable;

    private $startDate; // Variabel untuk menyimpan tanggal awal.
    private $endDate; // Variabel untuk menyimpan tanggal akhir.

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate; // Menginisialisasi variabel startDate dengan nilai yang diberikan.
        $this->endDate = $endDate; // Menginisialisasi variabel endDate dengan nilai yang diberikan.
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = []; // Membuat array kosong untuk menampung data yang akan diekspor.
        $data[] = ['Laporan', $this->startDate, $this->endDate]; // Menambahkan baris judul laporan ke dalam array data.
        $data[] = ['']; // Menambahkan baris kosong ke dalam array data.
        $data[] = ['tanggal', 'nama karyawan', 'kontak', 'total gaji', 'jumlah item']; // Menambahkan baris header kolom ke dalam array data.
        $items = Payroll::with('employee')->whereBetween('date', [$this->startDate, $this->endDate])->get(); // Mengambil data gaji dengan relasi karyawan antara rentang tanggal tertentu.
        foreach ($items as $item) {
            $data[] = [ // Menambahkan baris data gaji ke dalam array data.
                $item->date,
                $item->employee->name,
                $item->employee->whatsapp,
                $item->recived,
                $item->item_count,
            ];
        }

        return collect($data); // Mengembalikan data yang dikumpulkan dalam objek koleksi.
    }
}
