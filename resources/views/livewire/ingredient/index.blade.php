<?php

use Livewire\Volt\Component;

new class extends Component {}; ?>

<section class="w-full">
    <x-mary-header title="Lista składników" separator />
    <x-mary-button label='Dodaj składnik' class="btn-primary" wire:click="$dispatch('openIngredientModal')" />
    <livewire:ingredient.ingredient-form />
    <div class="mt-8"></div>
    <livewire:ingredient.ingredient-grid />
    <livewire:ingredient.ingredient-delete />
</section>
