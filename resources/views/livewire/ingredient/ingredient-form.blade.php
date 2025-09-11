<?php

use App\Models\Ingredient;
use Livewire\Volt\Component;

new class extends Component {
    public ?Ingredient $ingredient = null;
    public ?string $name = null;
    public bool $showModal = false;

    protected $listeners = ['openIngredientModal' => 'openModal'];

    public function save(): void
    {
        $id = $this->ingredient?->id;

        $this->validate(
            [
                'name' => ['required', 'string', 'max:255', 'unique:ingredients,name' . ($id ? ",$id" : '')],
            ],
            [
                'name.required' => 'Nazwa składnika jest wymagana.',
                'name.max' => 'Nazwa składnika nie może być dłuższa niż 255 znaków.',
                'name.unique' => 'Ten składnik już istnieje.',
            ],
        );

        if ($this->ingredient) {
            $this->ingredient->update(['name' => $this->name]);
            $this->dispatch('ingredientUpdated');
        } else {
            Ingredient::create(['name' => $this->name]);
            $this->dispatch('ingredientAdded');
        }

        $this->resetForm();
        $this->closeModal();
    }

    private function resetForm(): void
    {
        $this->ingredient = null;
        $this->name = '';
    }

    public function openModal(?int $id = null): void
    {
        if ($id) {
            $this->ingredient = Ingredient::findOrFail($id);
            $this->name = $this->ingredient->name;
        } else {
            $this->resetForm();
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }
};
?>
<div>
    <x-mary-modal wire:model="showModal" title="{{ $ingredient ? 'Edytuj składnik' : 'Dodaj składnik' }}">
        <x-mary-form wire:submit="save">
            <x-mary-input label="Nazwa składnika" wire:model="name" />
            <div class="flex justify-end gap-2 mt-4">
                <x-mary-button label="Anuluj" class="btn-ghost" wire:click="closeModal" />
                <x-mary-button label="Zapisz" class="btn-primary" type="submit" spinner="save" />
            </div>
        </x-mary-form>
    </x-mary-modal>
</div>
