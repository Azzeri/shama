<?php

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Meal;
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
}; ?>

<div>
    @php
        $currentWeekDate =
            isset($this->week) && count($this->week) ? $this->week[array_key_first($this->week)]['date'] : now();
        $firstDayOfWeek = Carbon::parse($currentWeekDate)->locale('pl')->startOfWeek()->translatedFormat('j F Y');
        $lastDayOfWeek = Carbon::parse($currentWeekDate)->locale('pl')->endOfWeek()->translatedFormat('j F Y');
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
    @foreach ($this->week as $day)
        @php
            $dateInLocale = $day['date']->locale('pl');
        @endphp
        <x-mary-card class="mt-4 bg-gray-100" shadow separator>
            <div class="flex">
                <div class="w-1/3 hover:cursor-pointer"
                    wire:click="$dispatch('openDayModal', { date: '{{ $dateInLocale->format('Y-m-d') }}' })">
                    <div class="font-semibold text-lg">{{ $dateInLocale->localeDayOfWeek }}</div>
                    <div class="text-sm text-gray-600">{{ $dateInLocale->translatedFormat('j F Y') }}</div>
                </div>

                <div class="w-2/3">
                    @forelse($day['meals'] as $meal)
                        <div class="py-1 font-bold">{{ $meal->type }}</div>
                        <div class="border-b">
                            <ul class="space-y-1 mb-2">
                                @foreach ($meal->recipes as $recipe)
                                    <li class="flex items-center pl-2 border-l-2 border-secondary">
                                        <span>{{ $recipe->name }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <div class="text-gray-500 text-sm">Brak posiłków</div>
                    @endforelse
                </div>
            </div>
        </x-mary-card>
    @endforeach
    <livewire:meal.day-modal />
</div>
