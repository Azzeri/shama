<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use App\Models\Recipe;
use App\Models\Meal;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Carbon\Carbon;

new class extends Component {
    public ShoppingList $shoppingList;
    public array $groupedItems = [];

    public function mount(int $shoppingListId): void
    {
        $this->shoppingList = ShoppingList::with(['shoppingListItems.meal', 'shoppingListItems.recipe'])->findOrFail($shoppingListId);
        $this->groupItems();
    }

    public function checkUncheckItem(int $itemId)
    {
        $item = ShoppingListItem::findOrFail($itemId);
        $item->isChecked = !$item->isChecked;
        $item->save();
        $this->groupItems();
    }

    public function groupItems()
    {
        $items = $this->shoppingList->shoppingListItems;
        $this->groupedItems = $items->groupBy(fn(ShoppingListItem $item) => $item->meal->date)->map(fn($itemsByDate) => $itemsByDate->groupBy(fn($item) => $item->meal->type)->map(fn($itemsByMeal) => $itemsByMeal->groupBy(fn($item) => $item->recipe?->name ?? 'Bez przepisu')))->toArray();
    }

    public function hasRecipeOnlyCheckedItems(array $items): bool
    {
        return collect($items)->every(fn($item) => $item['isChecked']);
    }

    public function hasDayOnlyCheckedItems(array $meals): bool
    {
        foreach ($meals as $mealType => $recipes) {
            foreach ($recipes as $recipeName => $items) {
                if (!collect($items)->every(fn($item) => $item['isChecked'])) {
                    return false;
                }
            }
        }
        return true;
    }
};
?>

<section class="w-full">
    @php
        $start = Carbon::parse($shoppingList->firstDay)->locale('pl')->translatedFormat('j F');
        $end = Carbon::parse($shoppingList->lastDay)->locale('pl')->translatedFormat('j F');
    @endphp
    @foreach ($this->groupedItems as $date => $meals)
        @if (!$this->hasDayOnlyCheckedItems($meals))
            <x-mary-header title="{{ Carbon::parse($date)->locale('pl')->localeDayOfWeek }}"
                subtitle="{{ Carbon::parse($date)->locale('pl')->translatedFormat('j F') }}" size="text-md"
                class="first:mt-0 mt-8" separator />
            @foreach ($meals as $mealType => $recipes)
                @foreach ($recipes as $recipeName => $items)
                    @if (!$this->hasRecipeOnlyCheckedItems($items))
                        <div class="mt-4"></div>
                        <span class="text-sm font-bold">{{ $mealType }}: {{ $recipeName }}</span>
                        <div class="grid grid-cols-3 gap-1 mt-2">
                            @foreach ($items as $item)
                                @if ($item['isChecked'] == false)
                                    <x-mary-card
                                        class="bg-primary/70 text-primary-content flex flex-col justify-between p-1 hover:cursor-pointer rounded-md shadow-md"
                                        wire:click="checkUncheckItem({{ $item['id'] }})">
                                        <div class="text-center font-semibold break-words text-sm">
                                            {{ $item['name'] }}
                                        </div>
                                        <div class="text-center text-xs mt-1">
                                            {{ $item['quantity'] }}
                                        </div>
                                    </x-mary-card>
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endforeach
            @endforeach
        @endif
    @endforeach
    <x-mary-header title="Kupione" size="text-md" class="mt-8" separator />
    <div class="grid grid-cols-3 gap-1">
        @foreach ($this->groupedItems as $date => $meals)
            @foreach ($meals as $mealType => $recipes)
                @foreach ($recipes as $recipeName => $items)
                    @foreach ($items as $item)
                        @if ($item['isChecked'] == true)
                            <x-mary-card
                                class="bg-neutral/10 flex flex-col justify-between p-1 hover:cursor-pointer rounded-md shadow-md"
                                wire:click="checkUncheckItem({{ $item['id'] }})">
                                <div class="text-center font-semibold break-words text-sm">
                                    {{ $item['name'] }}
                                </div>
                                <div class="text-center text-xs text-gray-700 mt-1">
                                    {{ $item['quantity'] }}
                                </div>
                                <div class="text-center text-xs text-gray-700 mt-1">
                                    {{ $recipeName }}
                                </div>
                            </x-mary-card>
                        @endif
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
    </div>


</section>
