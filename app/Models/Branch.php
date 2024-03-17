<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'm_branch';
    protected $primaryKey = 'branch_id';
    protected $guarded = [];
    public function products()
    {
        return $this->hasMany(Product::class, 'branch_id');
    }
}
