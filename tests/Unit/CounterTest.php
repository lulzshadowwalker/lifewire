<?php

namespace Tests\Unit\App\Livewire;

use App\Livewire\Counter;
use Livewire\Livewire;
use Tests\TestCase;

class CounterTest extends TestCase
{
    /** @test */
    public function it_increments_count_when_increment_button_is_clicked()
    {
        Livewire::test(Counter::class)
            ->call('increment')
            ->assertSet('count', 1);
    }

    /** @test */
    public function it_decrements_count_when_decrement_button_is_clicked()
    {
        Livewire::test(Counter::class)
            ->call('decrement')
            ->assertSet('count', -1);
    }
}
