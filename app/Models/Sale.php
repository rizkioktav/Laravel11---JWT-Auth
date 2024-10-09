<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';

    protected $fillable = [
        'sale_no',
        'customer_no',
        'product',
        'qty',
        'price',
        'discount',
        'tax',
        'total',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_no', 'customer_no');
    }
}
