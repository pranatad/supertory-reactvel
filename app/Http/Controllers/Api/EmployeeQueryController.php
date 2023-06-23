<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // Mengimport kelas Controller dari laravel
use Illuminate\Http\Request; // Mengimport kelas Request dari laravel
use App\Models\Employee; // Mengimport model Employee

class EmployeeQueryController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->q != null) { // Memeriksa apakah parameter 'q' dalam request tidak null
            $query = Employee::where('name', 'like', '%' . $request->q . '%')->orWhere('whatsapp', 'like', '%' . $request->q . '%')->orderBy('id');
            // Membuat query untuk mencari karyawan berdasarkan nama atau nomor WhatsApp yang cocok dengan parameter 'q'
            // Mengurutkan hasil berdasarkan id
        } else {
            $query = Employee::orderBy('id'); // Jika parameter 'q' null, maka query akan mengambil semua karyawan yang diurutkan berdasarkan id
        }

        return $query->limit(10)->get(); // Mengambil 10 data hasil query dan mengembalikannya dalam bentuk koleksi
    }
}
