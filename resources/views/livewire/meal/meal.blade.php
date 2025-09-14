<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use App\Models\Recipe;
use App\Models\Meal;
use Carbon\Carbon;

new class extends Component {
    public array $recipesDropdown;
    public ?int $recipeIdToAdd = null;
    public Meal $meal;

    protected $listeners = ['openDayModal' => 'openModal'];

    public function mount(int $mealId): void
    {
        $this->meal = Meal::findOrFail($mealId);
        $this->recipesDropdown = Recipe::query()->orderBy('name', 'asc')->get()->toArray();
    }

    public function addRecipeToMeal(): void
    {
        if (empty($this->recipeIdToAdd)) {
            return;
        }
        $recipeId = $this->recipeIdToAdd;
        $meal = $this->meal;

        if (!$meal->recipes->contains($recipeId)) {
            $meal->recipes()->attach($recipeId);
        }

        $meal->load('recipes');
        $this->recipeIdToAdd = null;
    }

    public function removeRecipeFromMeal(int $recipeId): void
    {
        $meal = $this->meal;
        $meal->recipes()->detach($recipeId);
        $meal->load('recipes');
    }

    public function removeMeal(int $mealId): void
    {
        $meal = Meal::findOrFail($mealId);
        $meal->delete();
        $this->dispatch('mealRemovedFromDay');
    }
}; ?>
<div class="mt-4 p-4 bg-white rounded-lg shadow-sm border">
    <div class="flex justify-between items-center mb-2">
        <div class="font-semibold text-lg text-gray-800">{{ $meal->type }}</div>
        <x-mary-button icon="o-trash" class="btn-sm" wire:click="removeMeal({{ $meal->id }})"
            spinner="removeMeal"></x-mary-button>
    </div>

    <ul class="space-y-1 mb-2">
        @foreach ($meal->recipes as $recipe)
            <li class="flex items-center pl-2 border-l-2 border-secondary">
                <span>{{ $recipe->name }}</span>
                <x-mary-button icon="o-trash" class="ml-2 btn-xs" wire:click="removeRecipeFromMeal({{ $recipe->id }})"
                    spinner="removeRecipeFromMeal"></x-mary-button>
            </li>
        @endforeach
    </ul>

    <div class="mt-8"></div>
    <x-mary-form wire:submit="addRecipeToMeal({{ $meal->id }})">
        <x-mary-choices-offline :options="$recipesDropdown" placeholder="Szukaj przepisu..." wire:model="recipeIdToAdd" single
            clearable searchable>
            <x-slot:append>
                <x-mary-button label="Dodaj" class="btn-primary" type="submit" spinner="addRecipeToMeal" />
            </x-slot:append>
        </x-mary-choices-offline>
    </x-mary-form>
</div>
