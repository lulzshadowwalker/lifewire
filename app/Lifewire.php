<?php

namespace App;

use Illuminate\Support\Facades\Blade;
use ReflectionClass;
use ReflectionProperty;

class Lifewire
{
    function initialRender($class)
    {
        $component = new $class();
        return Blade::render($component->render(), $this->getProperties($component));
    }

    function getProperties($component)
    {
        $props = [];

        $reflectedProps = (new ReflectionClass($component))->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($reflectedProps as $rf) {
            $props[$rf->getName()] = $rf->getValue($component);
        }

        return $props;
    }
}
