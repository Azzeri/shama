<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
class RecipeIngredientAssignment extends Pivot
{
    use HasFactory;
    protected $fillable = ['quantity'];
    protected $table = 'recipe_ingredient_assignments';
}
