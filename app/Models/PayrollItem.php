<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    // Mendefinisikan kolom-kolom yang dapat diisi (fillable) pada model PayrollItem
    protected $fillable = [
        'product_id',
        'payroll_id',
        'quantity',
        'price',
    ];

    // Menentukan model yang terkait untuk penghapusan terkait (cascade delete)
    protected $cascadeDeletes = ['payroll'];

    // Definisi relasi PayrollItem dengan Product (satu PayrollItem dimiliki oleh satu Product)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Definisi relasi PayrollItem dengan Payroll (satu PayrollItem dimiliki oleh satu Payroll)
    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
