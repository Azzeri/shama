<?php

use App\Models\Ingredient;
use App\Models\Recipe;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public int $perPage = 10;
    protected $listeners = [
        'recipeAdded' => '$refresh',
        'recipeDeleted' => '$refresh',
        'recipeUpdated' => '$refresh',
    ];

    public function fetchRecipes()
    {
        return Recipe::query()->orderBy('name', 'asc')->paginate($this->perPage);
    }

    public function openRecipe(int $recipeId)
    {
        $this->redirectRoute('recipe.show', ['recipeId' => $recipeId]);
    }
}; ?>

<div>
    @php
        $recipes = $this->fetchRecipes();
    @endphp
    <x-mary-button label='Dodaj przepis' class="btn-primary" wire:click="openRecipe(0)" />
    <div class="mt-8"></div>

    @foreach ($recipes as $recipe)
        <x-mary-list-item :item="$recipe">
            <x-slot:actions>
                <x-mary-button label="Edytuj" class="btn-sm" wire:click="openRecipe({{ $recipe->id }})"
                    wire:key="update-{{ $recipe->id }}" />

                <x-mary-button icon="o-trash" class="btn-sm"
                    wire:click="$dispatch('openRecipeDeleteConfirmationModal', { id: {{ $recipe->id }} })"
                    wire:key="delete-btn-{{ $recipe->id }}" />
            </x-slot:actions>
        </x-mary-list-item>
    @endforeach
    <x-mary-pagination :rows="$recipes" wire:model.live="perPage" />
</div>
