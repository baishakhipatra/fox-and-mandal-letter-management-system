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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('bookshelves_id');
            $table->integer('office_id');
            $table->string('uid');
            $table->string('qrcode');
            $table->integer('category_id');
            $table->string('title');
            $table->string('author');
            $table->string('publisher');
            $table->string('edition');
            $table->string('page');
            $table->string('quantity');
            $table->tinyInteger('status')->comment('1: active, 0: inactive')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
