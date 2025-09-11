<?php

use Livewire\Volt\Component;

new class extends Component {}; ?>

<section class="w-full">
    <x-mary-header title="Lista przepisów" separator />
    <x-mary-button label='Dodaj przepis' class="btn-primary" wire:click="$dispatch('openRecipeModal')" />
    <livewire:recipe.recipe-form />
    <div class="mt-8"></div>
    <livewire:recipe.recipe-grid />
    <livewire:recipe.recipe-delete />
</section>
