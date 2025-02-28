<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('login', 50)->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->timestamps();
        });
    }
    
    public function down() {
        Schema::dropIfExists('users');
    }
    
    
};
