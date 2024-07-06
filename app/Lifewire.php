<?php

namespace App;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Str;

class Lifewire
{
    function initialRender($class)
    {
        $component = new $class();

        if (method_exists($component, 'mount')) {
            $component->mount();
        }

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
        $this->verifyChecksum($snapshot);

        $class = $snapshot['class'];
        $data= $snapshot['data'];
        $metadata = $snapshot['metadata'];

        $component = new $class();

        $properties = $this->hydrate($data, $metadata);
        $this->setProperties($component, $properties);

        return $component;
    }

    function toSnapshot($component): array
    {
        $html = Blade::render(
            $component->render(),
            $properties = $this->getProperties($component)
        );

        [$data, $metadata] = $this->dehydrate($properties);

        $snapshot = [
            'class' => get_class($component),
            'data' => $data,
            'metadata' => $metadata,
        ];

        $snapshot['checksum'] = $this->generateChecksum($snapshot);

        return [$html, $snapshot];
    }

    function hydrate($data, $metadata)
    {
        $properties = [];

        foreach ($data as $key => $value) {
            if (isset($metadata[$key]) && $metadata[$key] === 'collection') {
                $value = collect($value);
            }

            $properties[$key] = $value;
        }

        return $properties;
    }

    function dehydrate($properties): array
    {
        $data = $metadata = [];

        foreach ($properties as $key => $value) {
            if ($value instanceof Collection) {
                $value = $value->toArray();
                $metadata[$key] = 'collection';
            }

            $data[$key] = $value;
        }

        return [$data, $metadata];
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
            $this->setProperty($component, $key, $value);
        }
    }

    function setProperty($component, $property, $value)
    {
        $component->$property = $value;

        $updatedHook = 'updated' . Str::title($property);
        if (method_exists($component, $updatedHook)) {
            $component->{$updatedHook}($value);
        }
    }

    function call($component, $action)
    {
        $component->{$action}();
    }

    function generateChecksum($snapshot)
    {
        // NOTE: better use a more secure hashing algorithm like HMAC and use hash_equals instead of !==
        // because "the hash can be recreated one character at a time by looking at the distribution
        // of the response times (!== will stop on the first character mismatch"
        return md5(json_encode($snapshot));
    }

    function verifyChecksum($snapshot)
    {
        $checksum = $snapshot['checksum'];
        unset($snapshot['checksum']);

        if ($this->generateChecksum($snapshot) !== $checksum) {
            throw new \Exception('Invalid checksum');
        }
    }
}
