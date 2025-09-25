<?php

use Livewire\Volt\Component;

new class extends Component {}; ?>

<section class="w-full">
    <x-mary-header title="Lista przepisów" separator size="text-lg" />
    <livewire:recipe.recipe-grid />
    <livewire:recipe.recipe-delete />
</section>
