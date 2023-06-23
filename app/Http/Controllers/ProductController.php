<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Mengecek apakah terdapat parameter pencarian (q) pada request
        if ($request->q != null) {
            // Jika ada, maka akan melakukan query dengan mencari produk berdasarkan nama atau deskripsi yang sesuai dengan parameter pencarian
            $query = Product::where('name', 'like', '%' . $request->q . '%')->orWhere('description', 'like', '%' . $request->q . '%')->orderBy('created_at', 'desc');
        } else {
            // Jika tidak ada, maka akan melakukan query untuk menampilkan semua produk secara terurut berdasarkan waktu dibuat
            $query = Product::orderBy('created_at', 'desc');
        }

        // Mengembalikan respons menggunakan template Inertia dengan data produk
        return inertia('Products', [
            'products' => $query->paginate(10),
            '_search' => $request->q ? $request->q : ''
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
        // Validasi data yang diterima dari permintaan
        $request->validate([
            'name' => 'required|string',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'photo' => 'nullable|image'
        ]);

        // Membuat instance baru dari model Product dengan menggunakan data yang diterima dari permintaan
        $product = Product::make($request->only(['name', 'price', 'description']));

        // Mengambil file foto dari permintaan
        $photo = $request->file('photo');

        // Jika ada file foto yang diunggah
        if ($photo != null) {
            // Menyimpan file foto ke penyimpanan (storage) dengan lokasi yang ditentukan
            $photo->store('public');
            // Menyimpan nama file foto yang dihasilkan ke dalam atribut "photo" dari objek product
            $product->photo = $photo->hashName();
        }

        // Menyimpan objek product ke dalam database
        $product->save();

        // Mengarahkan pengguna ke rute "products.index"
        return redirect()->route('products.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // Validasi data yang diterima dari permintaan
        $request->validate([
            'name' => 'required|string',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'photo' => 'nullable|image'
        ]);

        // Mengisi atribut-atribut objek product dengan data yang diterima dari permintaan
        $product->fill($request->only(['name', 'price', 'description']));

        // Mengambil file foto dari permintaan
        $photo = $request->file('photo');

        // Jika ada file foto yang diunggah
        if ($photo != null) {
            // Jika objek product memiliki foto sebelumnya
            if ($product->photo != null) {
                // Menghapus foto sebelumnya dari penyimpanan (storage)
                Storage::delete('public/' . $product->photo);
                // Mengatur atribut "photo" objek product menjadi null
                $product->photo = null;
            }
            // Menyimpan file foto ke penyimpanan (storage) dengan lokasi yang ditentukan
            $photo->store('public');
            // Menyimpan nama file foto yang dihasilkan ke dalam atribut "photo" dari objek product
            $product->photo = $photo->hashName();
        }

        // Menyimpan perubahan objek product ke dalam database
        $product->save();

        // Mengarahkan pengguna ke rute "products.index"
        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Jika objek product memiliki foto
        if ($product->photo != null) {
            // Menghapus foto dari penyimpanan (storage)
            Storage::delete('public/' . $product->photo);
        }

        // Menghapus objek product dari database
        $product->delete();

        // Mengarahkan pengguna ke rute "products.index"
        return redirect()->route('products.index');
    }
}
