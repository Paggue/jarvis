<?php

namespace Lara\Jarvis\Models\Traits;

use Illuminate\Support\Arr;

trait HasColumnMapping
{
    private array $mappedColumns = [
        'id' => 'id'
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->setup();

        parent::__construct($attributes);
    }

    /**
     * Setup private properties
     */
    private function setup()
    {
        $maps = property_exists($this, 'maps') && is_array($this->maps) ? $this->maps : [];

        $this->mappedColumns = array_merge(
            $this->mappedColumns,
            $maps
        );
    }

    /**
     * Convert model to a mapped attributes and relations array
     *
     * @return array
     */
    private function serialize(): array
    {
        $friendlyAttributes = [];

        $attributes = array_merge(
            $this->attributesToArray(),
            $this->relationsToArray()
        );

        foreach ($this->mappedColumns as $map => $column) {
            $friendlyAttributes = Arr::add($friendlyAttributes, $map, Arr::get($attributes, $column));
        }

        return !empty($friendlyAttributes)
            ? Arr::undot($friendlyAttributes)
            : $attributes;
    }


    /**
     * Fill model with mapped attributes with real database fields fallback
     *
     * @param array $friendlyAttributes
     * @return $this
     */
    public function fill(array $friendlyAttributes): self
    {
        $databaseAttributes = [];

        foreach (Arr::dot($this->mappedColumns) as $field => $column) {
            if (Arr::has(Arr::dot($friendlyAttributes), $field)) {
                $databaseAttributes = Arr::add($databaseAttributes, $column, Arr::get($friendlyAttributes, $field));
            }
        }

        parent::fill($friendlyAttributes); // allow adding not mapped fields to model
        parent::fill($databaseAttributes);

        return $this;
    }

    /**
     * Get an attribute from the serialized model.
     *
     * @param string $key
     * @return mixed
     */
    public function getMapped(string $key): mixed
    {
        return Arr::get($this->serialize(), $key);
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return parent::getAttribute(Arr::get($this->mappedColumns, $key, $key));
    }

    /**
     * Set an attribute to the model.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        return parent::setAttribute(Arr::get($this->mappedColumns, $key, $key), $value);
    }

    /**
     * Convert the model instance to serialized array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->serialize();
    }
}
