<?php
use App\Models\Recipe;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $recipeId = null;
    public bool $showDeleteModal = false;

    protected $listeners = ['openRecipeDeleteConfirmationModal' => 'openModal'];

    public function deleteRecipe(): void
    {
        $recipe = Recipe::find($this->recipeId);
        if ($recipe) {
            $recipe->delete();
            $this->closeModal();
            $this->dispatch('recipeDeleted');
        }
    }

    public function openModal(int $id): void
    {
        $this->recipeId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal(): void
    {
        $this->showDeleteModal = false;
    }

    public function getRecipeProperty(): ?Recipe
    {
        return $this->recipeId ? Recipe::find($this->recipeId) : null;
    }
}; ?>
<div>
    <x-mary-modal wire:model="showDeleteModal" title="Potwierdź usunięcie">
        <p>Czy na pewno chcesz usunąć przepis: {{ $this->recipe?->name }}?</p>
        <div class="flex justify-end gap-2 mt-4">
            <x-mary-button label="Anuluj" class="btn-ghost" wire:click="closeModal" />
            <x-mary-button label="Usuń" class="btn-error" wire:click="deleteRecipe" spinner />
        </div>
    </x-mary-modal>
</div>
