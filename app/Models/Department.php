<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'department_id', 'id');
    }
    public function subject()
    {
        return $this->hasOne('App\Models\Subject', 'category_id', 'id');
    }
}
