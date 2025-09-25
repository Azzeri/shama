<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use App\Models\Recipe;
use App\Models\Meal;
use Carbon\Carbon;

new class extends Component {
    public ?Carbon $date = null;
    public ?Collection $meals = null;
    public ?string $mealTypeToAdd = null;

    public function mount(string $dayDate): void
    {
        $this->date = Carbon::parse($dayDate);
        $this->meals = $this->getMealsForDay();
    }

    protected $listeners = [
        'mealRemovedFromDay' => 'getMealsForDay',
    ];

    public function addMealToADay(): void
    {
        if (empty($this->mealTypeToAdd)) {
            return;
        }

        $meals = $this->getMealsForDay();
        $meals = $meals->filter(fn(Meal $meal) => $meal->type === $this->mealTypeToAdd);
        if ($meals->isEmpty()) {
            Meal::create([
                'date' => $this->date?->format('Y-m-d'),
                'type' => $this->mealTypeToAdd,
            ]);
            $this->meals = $this->getMealsForDay();
        }
        $this->mealTypeToAdd = null;
    }

    public function getMealsForDay(): Collection
    {
        return Meal::whereDate('date', $this->date)->get();
    }
}; ?>
@php
    $dateInLocale = Carbon::parse($this->date)->locale('pl');
@endphp
<section class="w-full">
    <x-mary-header title="{{ $dateInLocale->localeDayOfWeek }}" subtitle="{{ $dateInLocale->translatedFormat('j F') }}"
        size="text-md" />

    @foreach ($this->meals ?? [] as $meal)
        <livewire:meal.meal :mealId="$meal->id" :key="$meal->id" />
    @endforeach

    <div class="mt-4 p-4 bg-white rounded-lg shadow-sm border">
        <x-mary-form wire:submit="addMealToADay">
            <x-mary-input label="Dodaj posiłek" placeholder="Dodaj posiłek..." wire:model='mealTypeToAdd' inline>
                <x-slot:append>
                    <x-mary-button label="Zapisz" class="join-item btn-primary" type="submit" spinner="addMealToADay" />
                </x-slot:append>
            </x-mary-input>
        </x-mary-form>
    </div>
</section>
