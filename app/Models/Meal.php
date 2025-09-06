<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meal extends Model
{
    protected $fillable = ['date', 'type'];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_meal_assignments')
            ->using(RecipeMealAssignment::class);
    }
}
