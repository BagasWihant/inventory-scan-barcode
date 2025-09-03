<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchDropdown extends Component
{

    public $method;
    public $onSelect;
    public $label;
    public $resetEvent;
    public $field;

    public function __construct($method, $onSelect = 'productSelected', $label, $field ,$resetEvent = null)
    {
        $this->method = $method;
        $this->onSelect = $onSelect;
        $this->label = $label;
        $this->resetEvent = $resetEvent;
        $this->field = $field;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-dropdown');
    }
}
