<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model {
    use HasFactory;

    protected $fillable = [
        'visitor_id', 'entry_time', 'exit_time', 'note'
    ];

    // Запись о посещении принадлежит посетителю
    public function visitor() {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }
}
