<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('dashboard', function () {
    return redirect()->route('meal.index');
})->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('ingredient', 'ingredient.index')->name('ingredient.index');
    Volt::route('recipe', 'recipe.index')->name('recipe.index');
    Volt::route('meal', 'meal.index')->name('meal.index');
    Volt::route('meal-day/{dayDate}', 'meal.day')->name('meal.day');
    Volt::route('shopping', 'shopping.index')->name('shopping.index');
    Volt::route('shopping-list-show/{shoppingListId}', componentName: 'shopping.shopping-list')
        ->name('shopping-list.show');
});

require __DIR__ . '/auth.php';
