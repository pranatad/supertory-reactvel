<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    // Mendefinisikan kolom-kolom yang dapat diisi (fillable) pada model Payroll
    protected $fillable = [
        'employee_id',
        'date',
        'amount',
        'cuts',
        'bonus',
        'item_count',
        'recived',
    ];

    // Definisi relasi Payroll dengan Employee (satu Payroll dimiliki oleh satu Employee)
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Definisi relasi Payroll dengan PayrollItem (satu Payroll memiliki banyak PayrollItem)
    public function items()
    {
        return $this->hasMany(PayrollItem::class);
    }
}
