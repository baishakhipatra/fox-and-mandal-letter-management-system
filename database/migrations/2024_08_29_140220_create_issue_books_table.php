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
        Schema::create('issue_books', function (Blueprint $table) {
            $table->id(); // Primary key for the table
            $table->unsignedBigInteger('book_id'); // Foreign key referencing books table
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key referencing users table
            $table->date('request_date'); // Date when the request was made
            $table->tinyInteger('status')->nullable()->comment('0 = reject, 1 = approve'); // Status column with integer values for reject and approve
            $table->date('approve_date')->nullable(); // Date when the request was approved, can be null
            $table->timestamps(); // created_at and updated_at columns

            // Define foreign key constraint for book_id
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_books');
    }
};
