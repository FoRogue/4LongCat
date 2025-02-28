<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model {
    use HasFactory;

    protected $fillable = ['name'];

    // Один отдел имеет много посетителей
    public function visitors() {
        return $this->hasMany(Visitor::class, 'department_id');
    }
}
