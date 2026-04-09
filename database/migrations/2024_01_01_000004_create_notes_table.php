<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('color', 7)->default('#FFFFFF');   // hex color e.g. #FFEB3B
            $table->string('theme')->default('default');       // e.g. default, dark, pastel
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
