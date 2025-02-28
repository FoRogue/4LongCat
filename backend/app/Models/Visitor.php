<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id', 'full_name', 'birth_date', 'position', 'phone',
        'department_id', 'document_type', 'document_series',
        'document_number', 'document_issue_date', 
        'document_issued_by', 'passport_code'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function visits() {
        return $this->hasMany(Visit::class, 'visitor_id');
    }
}
