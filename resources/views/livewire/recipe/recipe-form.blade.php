<?php

use App\Models\Recipe;
use App\Models\Ingredient;
use Livewire\Volt\Component;

new class extends Component {
    public ?Recipe $recipe = null;
    public ?string $name = null;
    public ?string $content = null;
    public array $selectedIngredients = [];
    public bool $showModal = false;
    public array $ingredientsDropdown;
    public ?int $ingredientIdToAdd = null;
    public ?string $ingredientQuantityToAdd = null;

    protected $listeners = ['openRecipeModal' => 'openModal'];

    public function mount(): void
    {
        $this->ingredientsDropdown = Ingredient::query()->orderBy('name', 'asc')->get()->toArray();
    }

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
        $pivotData = collect($this->selectedIngredients)->map(fn($q) => ['quantity' => $q])->toArray();

        if ($this->recipe) {
            $this->recipe->update(['name' => $this->name, 'content' => $this->content]);
            $this->recipe->ingredients()->sync($pivotData);
            $this->dispatch('recipeUpdated');
        } else {
            $recipe = Recipe::create(['name' => $this->name, 'content' => $this->content]);
            $recipe->ingredients()->attach($pivotData);
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
        $this->selectedIngredients = [];
        $this->resetIngredientForm();
    }

    private function resetIngredientForm(): void
    {
        $this->ingredientIdToAdd = null;
        $this->ingredientQuantityToAdd = null;
    }

    public function openModal(?int $id = null): void
    {
        if ($id) {
            $this->recipe = Recipe::findOrFail($id);
            $this->selectedIngredients = $this->recipe->ingredients->pluck('pivot.quantity', 'id')->toArray();
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
        $this->resetForm();
    }

    public function addIngredientToForm(): void
    {
        if (!$this->ingredientIdToAdd || !$this->ingredientQuantityToAdd) {
            return;
        }
        $this->selectedIngredients[$this->ingredientIdToAdd] = $this->ingredientQuantityToAdd;
        $this->resetIngredientForm();
    }

    public function removeIngredientFromForm(int $ingredientId): void
    {
        unset($this->selectedIngredients[$ingredientId]);
    }
};
?>
<div>
    <x-mary-modal wire:model="showModal" title="{{ $recipe ? 'Edytuj przepis' : 'Dodaj przepis' }}">
        <x-mary-form wire:submit="save">
            <x-mary-input label="Nazwa przepisu" wire:model="name" />
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <x-mary-choices-offline label="Dodaj składnik" :options="$ingredientsDropdown" placeholder="Szukaj ..."
                        wire:model="ingredientIdToAdd" single clearable searchable />
                </div>
                <div class="w-25">
                    <x-mary-input label="Ilość" wire:model="ingredientQuantityToAdd" placeholder="Podaj ilość..."
                        inline />
                </div>

                <x-mary-button label="Dodaj" class="btn-primary" spinner="addIngredientToForm"
                    wire:click="addIngredientToForm" />
            </div>
            <ul class="list-disc list-inside space-y-1 text-gray-800">

                @foreach ($selectedIngredients as $ingredientId => $quantity)
                    @php
                        $ingredientAsObject = Ingredient::find($ingredientId);
                    @endphp
                    <div class="flex">
                        <li class="text-sm font-medium">{{ $ingredientAsObject->name }} - {{ $quantity }}</li>
                        <x-mary-button icon="o-trash" class="ml-2 btn-xs"
                            wire:click="removeIngredientFromForm({{ $ingredientId }})" />
                    </div>
                @endforeach
            </ul>

            <x-textarea label="Opis" wire:model="content" placeholder="Krok 1..." hint="Max 500 chars"
                rows="15" />
            <div class="flex justify-end gap-2 mt-4">
                <x-mary-button label="Anuluj" class="btn-ghost" wire:click="closeModal" />
                <x-mary-button label="Zapisz" class="btn-primary" type="submit" spinner="save" />
            </div>
        </x-mary-form>
    </x-mary-modal>
</div>
