<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); 
            $table->string('name', 255)->nullable()->index();
            $table->string('email', 255)->unique()->index(); 
            $table->date('birth_date')->nullable(); 
            $table->string('cpf', 14)->unique()->nullable()->index(); 
            $table->text('google_token')->nullable(); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

