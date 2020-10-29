<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
    public function subject()
    {
        return $this->hasOne('App\Models\Subject', 'category_id', 'id');
    }
}
