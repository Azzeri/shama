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
    public string $date;
    public array $day;

    public function mount(string $date): void
    {
        $this->date = $date;

        $this->day = ['date' => Carbon::parse($date), 'meals' => Meal::whereDate('date', $date)->get()];
    }

    public function redirectToDay(): void
    {
        $this->redirectRoute('meal.day', ['dayDate' => $this->date]);
    }
}; ?>

<div>
    @php
        $dateInLocale = $this->day['date']->locale('pl');
    @endphp
    <x-mary-card wire:click="redirectToDay('{{ $dateInLocale->format('Y-m-d') }}')"
        class="mt-4 bg-gray-100 hover:cursor-pointer rounded-2xl shadow-md p-3 sm:p-4" shadow separator>
        <div class="flex flex-col gap-3">
            <div class="font-semibold text-base sm:text-lg text-gray-700 leading-tight">
                {{ $dateInLocale->localeDayOfWeek }}
            </div>

            <div>
                @forelse($this->day['meals'] as $meal)
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
</div>
