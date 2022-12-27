<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration

{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('image');
            $table->string('title')->nullable();
            $table->string('category')->nullable();
            $table->string('description')->nullable();
            $table->string('slug')->nullable();
            $table->uuid('crew_id')->nullable()->index();
            $table->boolean('is_live')->default(false);
            $table->boolean('closed_to_comments')->default(false);
            $table->boolean('upload_successful')->default(false);
            $table->string('disk')->default('public');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
