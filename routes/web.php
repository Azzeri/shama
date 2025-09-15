<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('ingredient', 'ingredient.index')->name('ingredient.index');
    Volt::route('recipe', 'recipe.index')->name('recipe.index');
    Volt::route('meal', 'meal.index')->name('meal.index');
    Volt::route('shopping', 'shopping.index')->name('shopping.index');
    Volt::route('shopping-list-show/{shoppingListId}', componentName: 'shopping.shopping-list')
        ->name('shopping-list.show');
});

require __DIR__ . '/auth.php';
