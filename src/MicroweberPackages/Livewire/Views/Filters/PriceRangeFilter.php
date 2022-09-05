<?php

namespace MicroweberPackages\Livewire\Views\Filters;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class PriceRangeFilter extends Filter
{
    public function validate($value)
    {
        return $value;
    }

    public function isEmpty($value): bool
    {
        return $value === '';
    }

    public function render(DataTableComponent $component)
    {
        return view('livewire::livewire.mw-livewire-tables.components.tools.filters.price-range', [
            'component' => $component,
            'filter' => $this,
        ]);
    }
}