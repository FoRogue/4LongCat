<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique()->nullable(); // Связь с user
            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('document_type')->nullable();
            $table->string('document_series')->nullable();
            $table->string('document_number')->nullable();
            $table->date('document_issue_date')->nullable();
            $table->string('document_issued_by')->nullable();
            $table->string('passport_code')->nullable();
            $table->timestamps();
    
            // Внешние ключи
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Убираем visitor_id
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }
    
    public function down() {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Удаляем внешний ключ
            $table->dropForeign(['department_id']); // Удаляем внешний ключ
        });
        Schema::dropIfExists('visitors');
    }
    
    
};
