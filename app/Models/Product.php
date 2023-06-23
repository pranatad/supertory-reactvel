<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    // Mendefinisikan kolom-kolom yang dapat diisi (fillable) pada model Product
    protected $fillable = [
        'name',
        'photo',
        'price',
        'description',
    ];

    // Menambahkan atribut virtual (accessor) untuk mengambil URL foto produk
    protected $appends = ['photo_url'];

    // Accessor untuk mendapatkan URL foto produk
    public function getPhotoUrlAttribute()
    {
        if ($this->photo != null) {
            return asset(Storage::url($this->photo));
        }
        return null;
    }

    // Definisi relasi One-to-Many antara Product dan PayrollItem (satu Product memiliki banyak PayrollItem)
    public function payrollItems()
    {
        return $this->hasMany(PayrollItem::class);
    }

    // Definisi relasi One-to-Many Through antara Product dan Payroll (satu Product memiliki banyak Payroll melalui PayrollItem)
    public function payrolls()
    {
        return $this->hasManyThrough(
            Payroll::class,
            PayrollItem::class,
            'product_id', // Foreign key pada tabel PayrollItem
            'id', // Local key pada tabel Product
            'id', // Local key pada tabel PayrollItem
            'payroll_id' // Foreign key pada tabel Payroll
        );
    }

    // Callback yang akan dijalankan sebelum Product dihapus
    protected static function booted()
    {
        static::deleting(function ($model) {
            // Menghapus semua PayrollItem yang terkait dan juga Payroll yang terkait jika ada
            if ($model->payrolls()->count() >= 1) {
                foreach ($model->payrolls as $payroll) {
                    $payroll->items()->delete();
                    $payroll->delete();
                }
            }
        });
    }
}
