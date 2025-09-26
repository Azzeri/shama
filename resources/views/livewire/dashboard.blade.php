<?php

use Livewire\Volt\Component;
use App\Models\ShoppingList;
use Carbon\Carbon;

new class extends Component {
    public $daysToDisplay = [];

    public function mount()
    {
        $this->generateDaysToDisplay();
    }

    public function getLatestShoppingList(): ShoppingList
    {
        return ShoppingList::latest()->first();
    }

    public function openList(int $list)
    {
        return redirect()->route('shopping-list.show', $list);
    }

    public function generateDaysToDisplay()
    {
        $this->daysToDisplay = [];
        $today = Carbon::now()->startOfDay();
        for ($i = 0; $i < 3; $i++) {
            $this->daysToDisplay[] = $today->copy()->addDays($i)->format('Y-m-d');
        }
    }
}; ?>

<section class="w-full">
    @php
        $latestShoppingList = $this->getLatestShoppingList();
    @endphp
    <div class="flex justify-center">
        <x-mary-card wire:click="openList({{ $latestShoppingList->id }})"
            class="w-60 bg-primary/70 text-primary-content  p-1 hover:cursor-pointer rounded-md shadow-md">
            <div class="text-center flex flex-col justify-between font-semibold break-words text-sm">
                <div>Lista zakupowa</div>
                <div class="text-xs mt-1">
                    {{ Carbon::parse($latestShoppingList->firstDay)->locale('pl')->translatedFormat('j F') }} -
                    {{ Carbon::parse($latestShoppingList->lastDay)->locale('pl')->translatedFormat('j F') }}
                </div>
            </div>
        </x-mary-card>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        @foreach ($this->daysToDisplay as $day)
            <livewire:meal.day-in-grid :date="$day" :key="$day" />
        @endforeach
</section>
