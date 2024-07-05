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
        [$html, $snapshot] = $this->toSnapshot($component);
        $snapshotAttr = htmlentities(json_encode($snapshot));

        return <<<HTML
                <div wire:snapshot="{$snapshotAttr}">
                    {$html}
                </div>
            HTML;
    }

    function fromSnapshot($snapshot)
    {
        $class = $snapshot['class'];
        $data= $snapshot['data'];

        $component = new $class();
        $this->setProperties($component, $data);

        return $component;
    }

    function toSnapshot($component): array
    {
        $html = Blade::render(
            $component->render(),
            $properties = $this->getProperties($component),
        );

        $snapshot = [
            'class' => get_class($component),
            'data' => $properties,
        ];

        return [$html, $snapshot];
    }

    function getProperties($component)
    {
        $properties = [];

        $reflectedproperties = (new ReflectionClass($component))->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($reflectedproperties as $rf) {
            $properties[$rf->getName()] = $rf->getValue($component);
        }

        return $properties;
    }

    function setProperties($component, $properties)
    {
        foreach ($properties as $key => $value) {
            $component->$key = $value;
        }
    }
    function call($component, $action)
    {
        $component->{$action}();
    }
}
