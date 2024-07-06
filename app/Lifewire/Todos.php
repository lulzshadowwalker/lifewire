<?php

namespace App\Lifewire;

class Todos
{
    public $prompt = '';
    public $todos;

    public function mount()
    {
        $this->todos = collect(['Learn Laravel']);
    }

    public function add()
    {
        if (!$this->prompt) return;

        $this->todos->push($this->prompt);
        $this->prompt = '';
    }

    public function render()
    {
        return
            <<<'HTML'
            <section>
                <h2>Todos</h2>
                <input type="text" wire:model="prompt" placeholder="What needs to be done?">
                <button wire:click="add">Add</button>

                <ul>
                    @foreach ($todos as $todo)
                        <li>{{ $todo }}</li>
                    @endforeach
                </ul>
            </section>
        HTML;
    }
}

