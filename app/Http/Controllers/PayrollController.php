<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Membuat query untuk mendapatkan data payroll dengan relasi employee
        $query = Payroll::with('employee');

        // Mengatur tanggal awal dan akhir bulan saat ini
        $startDate = now()->startOfMonth()->toDateString();
        $endDate = now()->endOfMonth()->toDateString();

        // Jika parameter startDate dan endDate diberikan, memperbarui query dan tanggal
        if ($request->startDate != null && $request->endDate != null) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
            $startDate = $request->startDate;
            $endDate = $request->endDate;
        } else {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        // Menampilkan tampilan "Payrolls/Index" dengan data payroll yang dipaginasi
        return inertia('Payrolls/Index', [
            'payrolls' => $query->orderBy('date', 'desc')->paginate(10),
            '_startDate' => $startDate,
            '_endDate' => $endDate
        ]);
    }

    /**
     * Created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Membuat query untuk mendapatkan produk dengan urutan berdasarkan ID secara menurun
        $query = Product::orderBy('id', 'desc');

        // Jika parameter "q" diberikan, memperbarui query dengan pencarian nama produk atau deskripsi produk
        if ($request->q != null) {
            $query = Product::where('name', 'like', '%' . $request->q . '%')
                ->orWhere('description', 'like', '%' . $request->q . '%')
                ->orderBy('created_at', 'desc');
        }

        // Menampilkan tampilan "Payrolls/Create" dengan data produk yang dipaginasi
        return inertia('Payrolls/Create', [
            'products' => $query->paginate(8),
            '_search' => $request->q ? $request->q : '',
            '_page' => $request->page ? $request->page : 1,
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
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'cuts' => 'nullable|numeric',
            'bonus' => 'nullable|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric'
        ]);

        // Mengubah format data item untuk disimpan
        $items = collect($request->items)->map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['quantity'] * $item['price']
            ];
        });

        // Menghitung jumlah total dan jumlah item
        $amount = $items->sum('subtotal');
        $itemCount = $items->sum('quantity');

        // Menghitung jumlah yang diterima setelah pemotongan dan bonus
        $recived = ($amount + $request->bonus) - $request->cuts;

        // Memulai transaksi database
        DB::beginTransaction();

        // Membuat data payroll baru dalam database
        $payroll = Payroll::create([
            'employee_id' => $request->employee_id,
            'date' => \Carbon\Carbon::parse($request->date)->toDateString(),
            'amount' => $amount,
            'cuts' => $request->cuts,
            'bonus' => $request->bonus,
            'item_count' => $itemCount,
            'recived' => $recived,
        ]);

        // Menyimpan item-item payroll ke database
        $payroll->items()->saveMany($items->mapInto(PayrollItem::class));

        // Menyimpan perubahan dalam database
        DB::commit();

        // Mengarahkan pengguna ke rute "payrolls.index"
        return redirect()->route('payrolls.index');
    }

    /**
     * Edit resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Payroll $payroll)
    {
        // Membuat query untuk mendapatkan produk dengan urutan berdasarkan ID secara menurun
        $query = Product::orderBy('id', 'desc');

        // Jika parameter "q" diberikan, memperbarui query dengan pencarian nama produk atau deskripsi produk
        if ($request->q != null) {
            $query = Product::where('name', 'like', '%' . $request->q . '%')
                ->orWhere('description', 'like', '%' . $request->q . '%')
                ->orderBy('created_at', 'desc');
        }

        // Menampilkan tampilan "Payrolls/Edit" dengan data payroll, produk, dan parameter pencarian
        return inertia('Payrolls/Edit', [
            'payroll' => $payroll->load(['items.product', 'employee']),
            'products' => $query->paginate(8),
            '_search' => $request->q ? $request->q : '',
            '_page' => $request->page ? $request->page : 1,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payroll $payroll)
    {
        // Melakukan validasi data yang diberikan melalui permintaan
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'cuts' => 'nullable|numeric',
            'bonus' => 'nullable|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric',
            'items.*.price' => 'required|numeric'
        ]);

        // Mengubah format data item untuk disimpan
        $items = collect($request->items)->map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['quantity'] * $item['price']
            ];
        });

        // Menghitung jumlah total dan jumlah item
        $amount = $items->sum('subtotal');
        $itemCount = $items->sum('quantity');

        // Menghitung jumlah yang diterima setelah pemotongan dan bonus
        $recived = ($amount + $request->bonus) - $request->cuts;

        // Memulai transaksi database
        DB::beginTransaction();

        // Memperbarui data payroll dalam database
        $payroll->update([
            'employee_id' => $request->employee_id,
            'date' => \Carbon\Carbon::parse($request->date)->toDateString(),
            'amount' => $amount,
            'cuts' => $request->cuts,
            'bonus' => $request->bonus,
            'item_count' => $itemCount,
            'recived' => $recived,
        ]);

        // Menghapus item-item payroll yang ada dan menyimpan item-item baru
        $payroll->items()->delete();
        $payroll->items()->saveMany($items->mapInto(PayrollItem::class));

        // Menyimpan perubahan dalam database
        DB::commit();

        // Mengarahkan pengguna ke rute "payrolls.index"
        return redirect()->route('payrolls.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payroll $payroll)
    {
        // Memulai transaksi database
        DB::transaction(function () use ($payroll) {
            // Menghapus item-item payroll yang ada
            $payroll->items()->delete();

            // Menghapus data payroll
            $payroll->delete();
        });

        // Mengarahkan pengguna ke rute "payrolls.index"
        return redirect()->route('payrolls.index');
    }

    /**
     * Generate PDF for the specified payroll.
     *
     * @param  Payroll  $payroll
     * @return \Illuminate\Http\Response
     */
    public function pdf(Payroll $payroll)
    {
        // Memuat tampilan PDF menggunakan library PDF (misalnya dompdf, mpdf, atau tcpdf)
        $pdf = PDF::loadView('payroll', [
            'payroll' => $payroll->load(['employee', 'items.product']),
            'user' => auth()->user()
        ]);

        // Mengunduh file PDF dengan nama berdasarkan nama pegawai dan tanggal payroll
        return $pdf->download($payroll->employee->name . '-' . $payroll->date . '.pdf');
    }
}
