<?php

use Livewire\Volt\Component;
use Illuminate\Support\Collection;
use App\Models\Recipe;
use App\Models\Meal;

new class extends Component {}; ?>

<section class="w-full">
    <x-mary-header title="Kalendarz" separator />
    <livewire:meal.meal-grid />
</section>
