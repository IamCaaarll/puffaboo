<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesDetail extends Model
{
    use HasFactory;

    protected $table = 't_purchases_detail';
    protected $primaryKey = 'purchase_detail_id';
    protected $guarded = [];

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id');
    }
}
