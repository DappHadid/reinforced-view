<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_pins', function (Blueprint $table) {
            $table->string('sinta_id', 20)->primary();
            $table->string('pin_hash');          // bcrypt hash PIN kustom
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pins');
    }
};
