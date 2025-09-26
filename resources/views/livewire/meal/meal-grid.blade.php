<?php

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Meal;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

new class extends Component {
    public array $week = [];
    protected $listeners = ['modalClosed' => 'generateWeeklyCalendar'];

    public function mount(): void
    {
        $this->generateWeeklyCalendar(now()->toDateString());
    }

    public function generateWeeklyCalendar(string $date): void
    {
        $date = Carbon::parse($date);
        $this->week = [];
        $firstDayOfWeek = $date->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $day = $firstDayOfWeek->copy()->addDays($i);
            if (Meal::whereDate('date', $day->format('Y-m-d'))->count() === 0) {
                $this->generateStandardMealsForADay($day);
            }
            $dayAsString = $day->format('Y-m-d');
            $this->week[$dayAsString] = ['date' => $day, 'meals' => Meal::whereDate('date', $dayAsString)->get()];
        }
    }

    public function generateStandardMealsForADay(Carbon $date): void
    {
        $standardMealTypes = ['Śniadanie', 'Lunch', 'Obiad', 'Kolacja'];
        foreach ($standardMealTypes as $mealType) {
            $existingMeal = Meal::whereDate('date', $date->format('Y-m-d'))->where('type', $mealType)->first();
            if (!$existingMeal) {
                Meal::create([
                    'date' => $date->format('Y-m-d'),
                    'type' => $mealType,
                ]);
            }
        }
    }

    public function generateShoppingList(): void
    {
        $shoppingList = ShoppingList::create([
            'firstDay' => $this->week[array_key_first($this->week)]['date']->format('Y-m-d'),
            'lastDay' => $this->week[array_key_last($this->week)]['date']->format('Y-m-d'),
        ]);
        foreach ($this->week as $day) {
            foreach ($day['meals'] as $meal) {
                foreach ($meal->recipes as $recipe) {
                    foreach ($recipe->ingredients as $ingredient) {
                        $item = new ShoppingListItem([
                            'name' => $ingredient->name,
                            'quantity' => $ingredient->pivot->quantity,
                            'isChecked' => false,
                            'notes' => '',
                        ]);
                        $item->shoppingList()->associate($shoppingList);
                        $item->meal()->associate($meal);
                        $item->recipe()->associate($recipe);
                        $item->save();
                    }
                }
            }
        }
    }
}; ?>

<div>
    @php
        $currentWeekDate =
            isset($this->week) && count($this->week) ? $this->week[array_key_first($this->week)]['date'] : now();
        $firstDayOfWeek = Carbon::parse($currentWeekDate)->locale('pl')->startOfWeek()->translatedFormat('j F');
        $lastDayOfWeek = Carbon::parse($currentWeekDate)->locale('pl')->endOfWeek()->translatedFormat('j F');
    @endphp
    <div class="flex justify-between">
        <x-mary-header title="{{ $firstDayOfWeek }} - {{ $lastDayOfWeek }}" size="text-md" />
        <div class="flex gap-2">
            <x-mary-button icon="o-arrow-up" class="btn-sm"
                wire:click="generateWeeklyCalendar('{{ Carbon::parse($currentWeekDate)->subWeek()->toDateString() }}')"
                spinner="generateWeeklyCalendar" />

            <x-mary-button icon="o-arrow-down" class="btn-sm"
                wire:click="generateWeeklyCalendar('{{ Carbon::parse($currentWeekDate)->addWeek()->toDateString() }}')"
                spinner="generateWeeklyCalendar" />
        </div>
    </div>
    <x-mary-button label="Przygotuj listę" class="btn-primary" wire:click="generateShoppingList"
        spinner="generateShoppingList" />

    @foreach ($this->week as $day)
        <livewire:meal.day-in-grid :date="$day['date']->format('Y-m-d')" :key="$day['date']->format('Y-m-d')" />
    @endforeach
</div>
