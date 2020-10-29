<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public function department() {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function category() {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }
}
