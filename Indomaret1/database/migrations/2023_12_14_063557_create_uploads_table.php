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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('file');
            $table->text('path');
            $table->string('file2')->nullable();
            $table->text('path2')->nullable();
            $table->string('file3')->nullable();
            $table->text('path3')->nullable();
            $table->string('file4')->nullable();
            $table->text('path4')->nullable();
            $table->string('file5')->nullable();
            $table->text('path5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
