<?php

use App\Models\Recipe;
use App\Models\Ingredient;
use Livewire\Volt\Component;

new class extends Component {
    public ?Recipe $recipe = null;
    public ?string $name = null;
    public ?string $content = null;
    public bool $showModal = false;

    protected $listeners = ['openRecipeModal' => 'openModal'];

    public function save(): void
    {
        $id = $this->recipe?->id;

        $this->validate(
            [
                'name' => ['required', 'string', 'max:255', 'unique:recipes,name' . ($id ? ",$id" : '')],
                'content' => ['required', 'string', 'max:500'],
            ],
            [
                'name.required' => 'Nazwa przepisu jest wymagana.',
                'name.max' => 'Nazwa przepisu nie może być dłuższa niż 255 znaków.',
                'name.unique' => 'Ten przepis już istnieje.',
                'content.required' => 'Treść przepisu jest wymagana.',
                'content.max' => 'Treść przepisu nie może być dłuższa niż 500 znaków.',
            ],
        );

        if ($this->recipe) {
            $this->recipe->update(['name' => $this->name, 'content' => $this->content]);
            $this->dispatch('recipeUpdated');
        } else {
            Recipe::create(['name' => $this->name, 'content' => $this->content]);
            $this->dispatch('recipeAdded');
        }

        $this->resetForm();
        $this->closeModal();
    }

    private function resetForm(): void
    {
        $this->recipe = null;
        $this->name = '';
        $this->content = '';
    }

    public function openModal(?int $id = null): void
    {
        if ($id) {
            $this->recipe = Recipe::findOrFail($id);
            $this->name = $this->recipe->name;
            $this->content = $this->recipe->content;
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
    <x-mary-modal wire:model="showModal" title="{{ $recipe ? 'Edytuj przepis' : 'Dodaj przepis' }}">
        <x-mary-form wire:submit="save">
            <x-mary-input label="Nazwa przepisu" wire:model="name" />
            <x-textarea wire:model="content" placeholder="Krok 1..." hint="Max 500 chars" rows="15" />
            <div class="flex justify-end gap-2 mt-4">
                <x-mary-button label="Anuluj" class="btn-ghost" wire:click="closeModal" />
                <x-mary-button label="Zapisz" class="btn-primary" type="submit" spinner="save" />
            </div>
        </x-mary-form>
    </x-mary-modal>
</div>
