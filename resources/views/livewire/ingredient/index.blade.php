<?php

use App\Models\Ingredient;
use Livewire\Volt\Component;

new class extends Component {
    public array $ingredients = [];
    public string $newName = '';
    public ?int $editId = null;
    public string $editName = '';

    public function mount(): void
    {
        $this->ingredients = Ingredient::all()->toArray();
    }

    public function addIngredient(): void
    {
        $this->validate([
            'newName' => ['required', 'string', 'max:255', 'unique:ingredients,name'],
        ]);

        Ingredient::create(['name' => $this->newName]);
        $this->newName = '';
        $this->ingredients = Ingredient::all()->toArray();
    }

    public function startEdit(int $id): void
    {
        $ingredient = Ingredient::find($id);
        if ($ingredient) {
            $this->editId = $id;
            $this->editName = $ingredient->name;
        }
    }

    public function updateIngredient(): void
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:255', 'unique:ingredients,name,' . $this->editId],
        ]);

        $ingredient = Ingredient::find($this->editId);
        if ($ingredient) {
            $ingredient->name = $this->editName;
            $ingredient->save();
        }
        $this->editId = null;
        $this->editName = '';
        $this->ingredients = Ingredient::all()->toArray();
    }

    public function cancelEdit(): void
    {
        $this->editId = null;
        $this->editName = '';
    }
}; ?>

<section class="w-full p-6">
    <h1 class="text-2xl font-bold mb-4">Lista składników</h1>

    <!-- Dodawanie składnika -->
    <form wire:submit="addIngredient" class="mb-6 flex gap-2">
        <input type="text" wire:model.live="newName" placeholder="Nowy składnik" class="border rounded px-2 py-1" />
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Dodaj</button>
    </form>

    <ul class="list-disc pl-6">
        @foreach ($ingredients as $ingredient)
            <li class="mb-2">
                @if ($editId === $ingredient['id'])
                    <form wire:submit="updateIngredient" class="inline-flex gap-2">
                        <input type="text" wire:model.live="editName" class="border rounded px-2 py-1" />
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">Zapisz</button>
                        <button type="button" wire:click="cancelEdit"
                            class="bg-gray-400 text-white px-3 py-1 rounded">Anuluj</button>
                    </form>
                @else
                    {{ $ingredient['name'] }}
                    <button type="button" wire:click="startEdit({{ $ingredient['id'] }})"
                        class="ml-2 text-blue-600 underline">Edytuj</button>
                @endif
            </li>
        @endforeach
    </ul>
</section>
