<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'content'];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class,'recipe_ingredient_assignments')
            ->using(RecipeIngredientAssignment::class)
            ->withPivot('quantity');
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'recipe_meal_assignments')
            ->using(RecipeMealAssignment::class);
    }
}
