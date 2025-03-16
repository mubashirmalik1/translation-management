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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            // Reference the locales table via a foreign key.
            $table->foreignId('locale_id')->constrained('locales')->onDelete('cascade');
            $table->string('translation_key')->unique();
            $table->text('translation_content');
            $table->timestamps();

            // Index for performance.
            $table->index('translation_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
