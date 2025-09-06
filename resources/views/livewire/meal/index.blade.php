<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use App\Models\Recipe;
use App\Models\Meal;

new class extends Component {
    /** @var Collection<int, Recipe> */
    public Collection $recipes;
    /** @var Collection<int, Meal> */
    public Collection $meals;
    public array $mealTypes;
    public bool $showModal = false;
    public string $selectedDate = '';
    public string $selectedType = '';
    public array $selectedRecipes = [];

    public function mount(): void
    {
        $this->recipes = Recipe::all();
        $this->meals = Meal::all();
        $this->mealTypes = ['breakfast', 'lunch', 'dinner', 'dessert'];
    }

    public function openModal(string $date): void
    {
        $this->showModal = true;
        $this->selectedDate = $date;
        $this->selectedType = '';
        $this->selectedRecipes = [];
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedDate = '';
        $this->selectedType = '';
        $this->selectedRecipes = [];
    }

    public function addMeal(): void
    {
        $meal = Meal::create([
            'date' => $this->selectedDate,
            'type' => $this->selectedType,
        ]);
        $meal->recipes()->attach($this->selectedRecipes);
        $this->meals = Meal::all();
        $this->closeModal();
    }
}; ?>

<section class="w-full p-6">
    <h1 class="text-2xl font-bold mb-4">Kalendarz posiłków</h1>

    <!-- Prosty kalendarz: 7 dni od dziś -->
    <div class="grid grid-cols-7 gap-2 mb-6">
        @for ($i = 0; $i < 7; $i++)
            @php
                $date = now()->addDays($i)->format('Y-m-d');
                $mealsForDay = $meals->where('date', $date);
            @endphp
            <div class="border rounded p-2 bg-zinc-100 hover:bg-blue-100 flex flex-col items-start">
                <button type="button" wire:click="openModal('{{ $date }}')" class="font-bold mb-2">
                    {{ $date }}
                </button>
                @forelse ($mealsForDay as $meal)
                    <div class="mb-1 text-sm">
                        <span class="font-semibold">{{ ucfirst($meal->type) }}</span>
                        @if ($meal->recipes->count())
                            <ul class="list-disc pl-4">
                                @foreach ($meal->recipes as $recipe)
                                    <li>{{ $recipe->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @empty
                    <span class="text-xs text-gray-400">Brak posiłków</span>
                @endforelse
            </div>
        @endfor
    </div>

    <!-- Modal dodawania posiłku -->
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded shadow-lg w-full max-w-md">
                <h2 class="text-xl font-bold mb-2">Dodaj posiłek na {{ $selectedDate }}</h2>
                <form wire:submit="addMeal" class="flex flex-col gap-4">
                    <div>
                        <label class="block mb-1">Typ posiłku:</label>
                        <select wire:model.live="selectedType" class="border rounded px-2 py-1 w-full">
                            <option value="">Wybierz typ</option>
                            @foreach ($mealTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1">Przepisy:</label>
                        <select wire:model.live="selectedRecipes" multiple class="border rounded px-2 py-1 w-full">
                            @foreach ($recipes as $recipe)
                                <option value="{{ $recipe->id }}">{{ $recipe->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-gray-500">Przytrzymaj Ctrl/Command, by wybrać kilka</small>
                    </div>
                    <button type="submit" class="bg-green-600 text-black px-4 py-2 rounded">Dodaj posiłek</button>
                    <button type="button" wire:click="closeModal"
                        class="bg-gray-400 text-black px-4 py-2 rounded">Anuluj</button>
                </form>
            </div>
        </div>
    @endif
</section>
