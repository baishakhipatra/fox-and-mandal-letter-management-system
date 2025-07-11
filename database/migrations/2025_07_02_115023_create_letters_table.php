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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_id')->unique();
            $table->string('received_from')->nullable();
            $table->string('handed_over_by')->nullable();
            $table->string('send_to')->nullable();
            $table->string('subject')->nullable();
            $table->string('document_reference_no')->nullable();
            $table->string('document_date')->nullable();
            $table->enum('status',['Delivered','Pending'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
