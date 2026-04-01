<?php

namespace App\Livewire;

use Livewire\Component;

class SimpleCounter extends Component
{
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function decrement(): void
    {
        $this->count--;
    }

    public function render()
    {
        return view('livewire.simple-counter');
    }
}
