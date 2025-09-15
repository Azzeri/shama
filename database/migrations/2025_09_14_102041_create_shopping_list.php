<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->date('firstDay');
            $table->date('lastDay');
            $table->timestamps();
        });

        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('quantity');
            $table->boolean('isChecked')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('shopping_list_id')
                ->constrained('shopping_lists')
                ->onDelete('cascade');
            $table->foreignId('recipe_id')
                ->nullable()
                ->constrained('recipes');
            $table->foreignId('meal_id')
                ->nullable()
                ->constrained('meals');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
        Schema::dropIfExists('shopping_lists');
    }
};
