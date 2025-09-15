<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingListItem extends Model
{
    protected $fillable = ['name', 'quantity', 'isChecked', 'notes'];

    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }
    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
