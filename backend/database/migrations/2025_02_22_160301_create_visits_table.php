<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visitor_id'); // Связь с visitor
            $table->dateTime('entry_time');
            $table->dateTime('exit_time');
            $table->text('note')->nullable();
            $table->foreign('visitor_id')->references('id')->on('visitors')->onDelete('cascade');
            $table->timestamps();
        });
    }
    
    public function down() {
        Schema::dropIfExists('visits');
    }
    
};

