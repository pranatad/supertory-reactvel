<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Membuat query untuk mengambil data pengguna yang diurutkan berdasarkan id
        $query = User::orderBy('id');

        // Jika terdapat parameter pencarian (q) yang diberikan dalam permintaan, menambahkan kondisi pencarian ke query
        if ($request->q != null) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Mengembalikan respons menggunakan template Inertia dengan data pengguna
        return inertia('Users', [
            'users' => $query->paginate(10),
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
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Membuat pengguna baru dengan menggunakan data yang diberikan dalam permintaan
        User::create($request->only(['name', 'email', 'password']));

        return redirect()->route('users.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
        ]);

        // Memperbarui data pengguna dengan menggunakan data yang diberikan dalam permintaan
        $user->update($request->only(['name', 'email']));
        if ($request->password != null) {
            $user->update(['password' => $request->password]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Menghapus pengguna yang ditentukan
        $user->delete();
        return redirect()->back();
    }
}
