<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Jika parameter "q" tidak kosong
        if ($request->q != null) {
            // Membuat query untuk mencari karyawan berdasarkan nama atau nomor WhatsApp
            $query = Employee::where('name', 'like', '%' . $request->q . '%')->orWhere('whatsapp', 'like', '%' . $request->q . '%')->orderBy('id');
        } else {
            // Membuat query untuk menampilkan semua karyawan dan diurutkan berdasarkan ID
            $query = Employee::orderBy('id');
        }

        // Menampilkan tampilan "Employees" dengan data karyawan yang dipaginasi
        return inertia('Employees', [
            'employees' => $query->paginate(10),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Melakukan validasi data yang diberikan melalui permintaan
        $request->validate([
            'name' => 'required|string',
            'whatsapp' => 'nullable|numeric',
            'basic_salary' => 'nullable|numeric',
            'photo' => 'nullable|image'
        ]);

        // Membuat instance Employee baru dengan data yang diberikan
        $employee = Employee::make($request->only(['name', 'whatsapp', 'basic_salary']));

        // Jika gaji dasar tidak diberikan, atur nilainya menjadi 0
        if ($request->basic_salary == null) {
            $employee->basic_salary = 0;
        }

        // Memproses foto jika ada yang diberikan
        $photo = $request->file('photo');
        if ($photo != null) {
            // Menyimpan foto ke penyimpanan dan mengatur nama file foto
            $photo->store('public');
            $employee->photo = $photo->hashName();
        }

        // Menyimpan data karyawan ke database
        $employee->save();

        // Mengarahkan pengguna ke rute "employees.index"
        return redirect()->route('employees.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        // Melakukan validasi data yang diberikan melalui permintaan
        $request->validate([
            'name' => 'required|string',
            'whatsapp' => 'nullable|numeric',
            'basic_salary' => 'nullable|numeric',
            'photo' => 'nullable|image'
        ]);

        // Mengisi data karyawan yang ada dengan data yang diberikan
        $employee->fill($request->only(['name', 'whatsapp', 'basic_salary']));

        // Jika gaji dasar tidak diberikan, atur nilainya menjadi 0
        if ($request->basic_salary === null) {
            $employee->basic_salary = 0;
        }

        // Memproses foto jika ada yang diberikan
        $photo = $request->file('photo');
        if ($photo != null) {
            // Menghapus foto yang ada (jika ada)
            if ($employee->photo != null) {
                Storage::delete('public/' . $employee->photo);
                $employee->photo = null;
            }
            // Menyimpan foto baru ke penyimpanan dan mengatur nama file foto
            $photo->store('public');
            $employee->photo = $photo->hashName();
        }

        // Menyimpan data karyawan yang diperbarui ke database
        $employee->save();

        // Mengarahkan pengguna ke rute "employees.index"
        return redirect()->route('employees.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        // Jika karyawan memiliki foto, hapus foto dari penyimpanan
        if ($employee->photo != null) {
            Storage::delete('public/' . $employee->photo);
        }

        // Menghapus data karyawan dari database
        $employee->delete();

        // Mengarahkan pengguna ke rute "employees.index"
        return redirect()->route('employees.index');
    }
}
