<?php

namespace App\Lifewire;

class Counter
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return
            <<<'HTML'
            <section>
                <h2>Counter</h2>
                <button wire:click="decrement">➖</button>
                <span>{{ $count }}</span>
                <button wire:click="increment">➕</button>
            </section>
        HTML;
    }
}
