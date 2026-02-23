<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('changelog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('changelog_id')->constrained()->onDelete('cascade');
            $table->enum('category', ['feature', 'improvement', 'bugfix', 'hotfix']);
            $table->text('description');
            $table->integer('sort_order')->default(0);
        });

        Schema::table('changelogs', function (Blueprint $table) {
            $table->dropColumn(['category', 'content']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('changelog_items');

        Schema::table('changelogs', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->text('content')->nullable();
        });
    }
};
