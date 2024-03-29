<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    use HasFactory;

    protected $table = 't_purchases';
    protected $primaryKey = 'purchase_id';
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }
    
    public function purchasesDetails()
    {
        return $this->hasMany(PurchasesDetail::class, 'purchase_id', 'purchase_id');
    }
}
