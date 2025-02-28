<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('deleted_records', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->json('data');
            $table->timestamp('deleted_at')->useCurrent();
        });
    }

    public function down() {
        Schema::dropIfExists('deleted_records');
    }
};

