<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ReceptionNote;
use App\Models\Company;

class LivewireHome extends Component
{

    public function render()
    {
        return view('livewire.inicio.index');
    }
}
