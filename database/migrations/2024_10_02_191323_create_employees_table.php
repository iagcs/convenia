<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function(Blueprint $table){
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')->constrained('users');

            $table->string('name');
            $table->string('email')->unique();
            $table->string('cpf');
            $table->string('city');
            $table->string('state');

            $table->timestamps(3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
