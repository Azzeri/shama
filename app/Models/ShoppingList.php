<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingList extends Model
{
    protected $fillable = ['firstDay', 'lastDay'];

    public function shoppingListItems(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class);
    }
}
