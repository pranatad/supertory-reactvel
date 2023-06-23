<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Mendefinisikan kolom-kolom yang dapat diisi (fillable) pada model Employee
    protected $fillable = [
        'name',
        'photo',
        'whatsapp',
        'basic_salary',
    ];

    // Menambahkan atribut tambahan (appends) pada model Employee
    protected $appends = ['photo_url'];

    // Definisi aksesornya (accessor) untuk mengambil URL foto
    public function getPhotoUrlAttribute()
    {
        if ($this->photo != null) {
            return asset(Storage::url($this->photo));
        }
        return null;
    }

    // Definisi relasi Employee dengan Payroll (satu Employee memiliki banyak Payroll)
    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    // Definisi hook (booted) yang akan dijalankan saat model Employee dihapus
    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->payrolls()->count() >= 1) {
                foreach ($model->payrolls as $payroll) {
                    $payroll->items()->delete();
                    $payroll->delete();
                }
            }
        });
    }
}
