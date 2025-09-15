<?php
use App\Models\ShoppingList;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $listId = null;
    public bool $showDeleteModal = false;

    protected $listeners = ['openShoppingListDeleteConfirmationModal' => 'openModal'];

    public function deleteList(): void
    {
        $list = ShoppingList::find($this->listId);
        if ($list) {
            $list->delete();
            $this->closeModal();
            $this->dispatch('shoppingListDeleted');
        }
    }

    public function openModal(int $id): void
    {
        $this->listId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal(): void
    {
        $this->showDeleteModal = false;
    }

    public function getListProperty(): ?ShoppingList
    {
        return $this->listId ? ShoppingList::find($this->listId) : null;
    }
}; ?>
<div>
    <x-mary-modal wire:model="showDeleteModal" title="Potwierdź usunięcie">
        <p>Czy na pewno chcesz usunąć listę: #{{ $this->list?->id }}?</p>
        <div class="flex justify-end gap-2 mt-4">
            <x-mary-button label="Anuluj" class="btn-ghost" wire:click="closeModal" />
            <x-mary-button label="Usuń" class="btn-error" wire:click="deleteList" spinner />
        </div>
    </x-mary-modal>
</div>
