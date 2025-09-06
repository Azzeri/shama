<?php

use App\Models\Recipe;
use App\Models\Ingredient;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Collection;

new class extends Component {
    /** @var Collection<int, Recipe> */
    public Collection $recipes;
    /** @var Collection<int, Ingredient> */
    public Collection $ingredients;
    public ?int $editedRecipeId = null;
    public string $newIngredientName = '';
    public string $newIngredientQuantity = '';
    public string $editedRecipeName = '';
    public string $editedRecipeContent = '';
    public bool $createModal = false;
    public string $newRecipeName = '';
    public string $newRecipeContent = '';
    public string $newIngredientNameCustom = '';

    public function mount(): void
    {
        $this->recipes = Recipe::all();
        $this->ingredients = Ingredient::all();
    }

    public function showModal(Recipe $recipe): void
    {
        $this->editedRecipeId = $recipe->id;
        $this->editedRecipeName = $recipe->name;
        $this->editedRecipeContent = $recipe->content;
    }

    public function closeModal(): void
    {
        $this->editedRecipeId = null;
        $this->newIngredientQuantity = '';
        $this->newIngredientName = '';
        $this->editedRecipeName = '';
        $this->editedRecipeContent = '';
    }

    public function removeIngredient(int $recipeId, int $ingredientId): void
    {
        $recipe = Recipe::find($recipeId);
        if ($recipe) {
            $recipe->ingredients()->detach($ingredientId);
            $this->recipes = Recipe::all();
        }
    }

    public function updateRecipeName(): void
    {
        if ($this->editedRecipeId) {
            $recipe = Recipe::find($this->editedRecipeId);
            if ($recipe) {
                $recipe->name = $this->editedRecipeName;
                $recipe->save();
                $this->recipes = Recipe::all();
            }
        }
    }

    public function updateRecipeContent(): void
    {
        if ($this->editedRecipeId) {
            $recipe = Recipe::find($this->editedRecipeId);
            if ($recipe) {
                $recipe->content = $this->editedRecipeContent;
                $recipe->save();
                $this->recipes = Recipe::all();
            }
        }
    }

    public function upsertIngredient(int $recipeId): void
    {
        $ingredientName = $this->newIngredientName === '_other' ? $this->newIngredientNameCustom : $this->newIngredientName;
        $ingredient = Ingredient::firstOrCreate(['name' => $ingredientName]);
        $recipe = Recipe::find($recipeId);
        if ($recipe) {
            // Sprawdź, czy składnik już jest przypisany
            if ($recipe->ingredients()->where('ingredient_id', $ingredient->id)->exists()) {
                // Jeśli jest, zaktualizuj ilość
                $recipe->ingredients()->updateExistingPivot($ingredient->id, [
                    'quantity' => $this->newIngredientQuantity,
                ]);
            } else {
                // Jeśli nie, przypisz z ilością
                $recipe->ingredients()->attach($ingredient->id, [
                    'quantity' => $this->newIngredientQuantity,
                ]);
            }
            $this->recipes = Recipe::all();
            $this->newIngredientName = '';
            $this->newIngredientQuantity = '';
            // $this->editedRecipeId = null; // Utrzymaj modal otwarty
            // $this->editedRecipeName = '';
            // $this->editedRecipeContent = '';
        }
    }
    public function showCreateModal(): void
    {
        $this->createModal = true;
        $this->newRecipeName = '';
        $this->newRecipeContent = '';
    }

    public function closeCreateModal(): void
    {
        $this->createModal = false;
        $this->newRecipeName = '';
        $this->newRecipeContent = '';
    }

    public function createRecipe(): void
    {
        $this->validate([
            'newRecipeName' => ['required', 'string', 'max:255'],
            'newRecipeContent' => ['nullable', 'string'],
        ]);

        Recipe::create([
            'name' => $this->newRecipeName,
            'content' => $this->newRecipeContent,
        ]);
        $this->recipes = Recipe::all();
        $this->closeCreateModal();
    }

    public function deleteRecipe(int $recipeId): void
    {
        $recipe = Recipe::find($recipeId);
        if ($recipe) {
            $recipe->delete();
            $this->recipes = Recipe::all();
        }
    }
}; ?>

<section class="w-full p-6">
    <button type="button" wire:click="showCreateModal" class="mb-4 bg-green-600 text-black px-4 py-2 rounded">
        Dodaj nowy przepis
    </button>
    <h1 class="text-2xl font-bold mb-4">Lista przepisów</h1>

    <ul class="list-disc pl-6">
        @foreach ($recipes as $recipe)
            <li class="mb-2">
                <button type="button" wire:click="showModal({{ $recipe }})" class="text-blue-600 underline">
                    {{ $recipe->id }}: {{ $recipe->name }}
                </button>
                <button type="button" wire:click="deleteRecipe({{ $recipe->id }})"
                    class="ml-2 bg-red-600 text-black px-2 py-1 rounded">Usuń</button>
            </li>
        @endforeach
    </ul>

    @if ($editedRecipeId)
        @php
            $modalRecipe = $recipes->firstWhere('id', $editedRecipeId);
        @endphp
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded shadow-lg w-full max-w-md">
                <form wire:submit="updateRecipeName" class="mb-6 flex gap-2">
                    <input type="text" wire:model.live="editedRecipeName" class="border rounded px-2 py-1" />
                    <button type="submit" class="bg-blue-600 text-black px-4 py-1 rounded">Zapisz</button>
                </form>
                <form wire:submit="updateRecipeContent" class="mb-6 flex gap-2">
                    <textarea wire:model.live="editedRecipeContent" class="border rounded px-2 py-1 w-full min-h-[100px]"
                        placeholder="Opis przepisu"></textarea>
                    <button type="submit" class="bg-blue-600 text-black px-4 py-1 rounded">Zapisz</button>
                </form>
                <form wire:submit="upsertIngredient({{ $modalRecipe->id }})" class="mb-6 flex gap-2">
                    <select wire:model.live="newIngredientName" class="border rounded px-2 py-1">
                        <option value="">Wybierz składnik</option>
                        <option value="_other">Inny...</option>
                        @foreach ($this->ingredients as $ingredient)
                            <option value="{{ $ingredient->name }}">{{ $ingredient->name }}</option>
                        @endforeach
                    </select>
                    @if ($newIngredientName === '_other')
                        <input type="text" wire:model.live="newIngredientNameCustom"
                            placeholder="Wpisz nowy składnik" class="border rounded px-2 py-1" />
                    @endif
                    <input type="text" wire:model.live="newIngredientQuantity" placeholder="Dodaj ilość"
                        class="border rounded px-2 py-1" />
                    <button type="submit" class="bg-blue-600 text-black px-4 py-1 rounded">Dodaj</button>
                </form>
                <ul>
                    @foreach ($modalRecipe->ingredients as $ingredient)
                        <li>
                            {{ $ingredient->name }}: {{ $ingredient->pivot->quantity }} <button type="button"
                                wire:click="removeIngredient({{ $modalRecipe->id }},{{ $ingredient->id }})">X</button>
                        </li>
                    @endforeach
                </ul>
                <button type="button" wire:click="closeModal"
                    class="bg-blue-600 text-black px-4 py-2 rounded">Zamknij</button>
            </div>
        </div>
    @endif
    @if ($createModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-800 p-6 rounded shadow-lg w-full max-w-md">
                <form wire:submit="createRecipe" class="mb-6 flex flex-col gap-2">
                    <input type="text" wire:model.live="newRecipeName" class="border rounded px-2 py-1"
                        placeholder="Nazwa przepisu" />
                    <textarea wire:model.live="newRecipeContent" class="border rounded px-2 py-1 w-full min-h-[100px]"
                        placeholder="Opis przepisu"></textarea>
                    <button type="submit" class="bg-green-600 text-black px-4 py-1 rounded">Utwórz</button>
                </form>
                <button type="button" wire:click="closeCreateModal"
                    class="bg-gray-400 text-black px-4 py-2 rounded">Anuluj</button>
            </div>
        </div>
    @endif
</section>
