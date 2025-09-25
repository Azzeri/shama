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
        @php
            $dateInLocale = $day['date']->locale('pl');
        @endphp
        <x-mary-card class="mt-4 bg-gray-100 hover:cursor-pointer rounded-2xl shadow-md p-3 sm:p-4" shadow separator>
            <div class="flex flex-col gap-3">
                <div wire:click="$dispatch('openDayModal', { date: '{{ $dateInLocale->format('Y-m-d') }}' })"
                    class="cursor-pointer">
                    <div class="font-semibold text-base sm:text-lg text-gray-700 leading-tight">
                        {{ $dateInLocale->localeDayOfWeek }}
                    </div>
                </div>

                <div>
                    @forelse($day['meals'] as $meal)
                        <div class="pt-2 font-bold text-sm sm:text-base">
                            {{ $meal->type }}
                        </div>
                        <div class="border-b pb-2 mt-1">
                            <ul class="space-y-1 sm:space-y-2 mb-2">
                                @foreach ($meal->recipes as $recipe)
                                    <li class="flex items-center pl-3 border-l-2 border-secondary text-sm sm:text-base">
                                        <span>{{ $recipe->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <div class="text-gray-500 text-xs sm:text-sm">Brak posiłków</div>
                    @endforelse
                </div>
            </div>
        </x-mary-card>
    @endforeach
    <livewire:meal.day-modal />
</div>
