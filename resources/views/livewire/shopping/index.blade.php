<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use App\Models\Recipe;
use App\Models\Meal;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Carbon\Carbon;

new class extends Component {
    public Collection $shoppingLists;
    public array $groupedItems = [];
    public bool $showDeleteModal = false;

    protected $listeners = [
        'shoppingListDeleted' => '$refresh',
    ];

    public function mount(): void
    {
        $this->shoppingLists = $this->getShoppingLists();
    }

    public function openList(ShoppingList $list)
    {
        return redirect()->route('shopping-list.show', $list->id);
    }

    public function getShoppingLists(): Collection
    {
        return ShoppingList::with('shoppingListItems')->orderBy('id', 'desc')->get();
    }
}; ?>

<section class="w-full">
    <x-mary-header title="Listy zakupowe" separator size="text-lg" />

    @foreach ($shoppingLists as $list)
        <x-mary-list-item :item="$list" link="shopping-list-show/{{ $list->id }}" class="hover:cursor-pointer">
            <x-slot:value>
                {{ 'Lista #' . $list->id }}
            </x-slot:value>
            <x-slot:sub-value>
                {{ Carbon::parse($list->firstDay)->locale('pl')->translatedFormat('j F') }} -
                {{ Carbon::parse($list->lastDay)->locale('pl')->translatedFormat('j F') }}
            </x-slot:sub-value>
            <x-slot:actions>
                <x-mary-button icon="o-trash" class="btn-sm"
                    wire:click="$dispatch('openShoppingListDeleteConfirmationModal', { id: {{ $list->id }} })"
                    wire:key="delete-btn-{{ $list->id }}" />
            </x-slot:actions>
        </x-mary-list-item>
    @endforeach
    <livewire:shopping.shopping-list-delete />
</section>
