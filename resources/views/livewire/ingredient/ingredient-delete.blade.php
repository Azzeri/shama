<?php
use App\Models\Ingredient;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $ingredientId = null;
    public bool $showDeleteModal = false;

    protected $listeners = ['openIngredientDeleteConfirmationModal' => 'openModal'];

    public function deleteIngredient(): void
    {
        $ingredient = Ingredient::find($this->ingredientId);
        if ($ingredient) {
            $ingredient->delete();
            $this->closeModal();
            $this->dispatch('ingredientDeleted');
        }
    }

    public function openModal(int $id): void
    {
        $this->ingredientId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal(): void
    {
        $this->showDeleteModal = false;
    }

    public function getIngredientProperty(): ?Ingredient
    {
        return $this->ingredientId ? Ingredient::find($this->ingredientId) : null;
    }
}; ?>
<div>
    <x-mary-modal wire:model="showDeleteModal" title="Potwierdź usunięcie">
        <p>Czy na pewno chcesz usunąć składnik: {{ $this->ingredient?->name }}?</p>
        <div class="flex justify-end gap-2 mt-4">
            <x-mary-button label="Anuluj" class="btn-ghost" wire:click="closeModal" />
            <x-mary-button label="Usuń" class="btn-error" wire:click="deleteIngredient" spinner />
        </div>
    </x-mary-modal>
</div>
