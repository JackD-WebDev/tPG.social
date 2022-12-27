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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('task_type', ['feature', 'issue', 'admin'])->default('feature');
            $table->enum('priority', ['essential', 'desired', 'deferrable'])->default('essential');
            $table->enum('location', ['api', 'client', 'infrastructure'])->default('api');
            $table->text('notes')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
