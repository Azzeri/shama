<?php

use App\Models\Ingredient;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public int $perPage = 10;
    protected $listeners = [
        'ingredientAdded' => '$refresh',
        'ingredientDeleted' => '$refresh',
        'ingredientUpdated' => '$refresh',
    ];

    public function fetchIngredients()
    {
        return Ingredient::query()->orderBy('name', 'asc')->paginate($this->perPage);
    }
}; ?>

<div>
    @php
        $ingredients = $this->fetchIngredients();
    @endphp
    @foreach ($ingredients as $ingredient)
        <x-mary-list-item :item="$ingredient">
            <x-slot:actions>
                <x-mary-button label="Edytuj" class="btn-sm"
                    wire:click="$dispatch('openIngredientModal', { id: {{ $ingredient->id }} })"
                    wire:key="update-{{ $ingredient->id }}" />

                <x-mary-button icon="o-trash" class="btn-sm"
                    wire:click="$dispatch('openIngredientDeleteConfirmationModal', { id: {{ $ingredient->id }} })"
                    wire:key="delete-btn-{{ $ingredient->id }}" />
            </x-slot:actions>
        </x-mary-list-item>
    @endforeach
    <x-mary-pagination :rows="$ingredients" wire:model.live="perPage" />
</div>
