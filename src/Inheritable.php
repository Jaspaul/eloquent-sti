<?php

namespace Jaspaul\EloquentSTI;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait Inheritable
{
    /**
     * Returns a collection of the types to map for inheritance.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getTypes() : Collection
    {
        if (is_array($this->types)) {
            return new Collection($this->types);
        }

        return new Collection([]);
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     *
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $type = Arr::get($attributes, 'type');

        if ($this->getTypes()->has($type)) {
            $class = $this->getTypes()->get($type);
            $model = new $class((array) $attributes);
        } else {
            $model = new static((array) $attributes);
        }

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        return $model;
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @param  string|null  $connection
     *
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance(Arr::only((array) $attributes, ['type']), true);
        $model->setRawAttributes((array) $attributes, true);
        $model->setConnection($connection ?: $this->getConnectionName());
        $model->fireModelEvent('retrieved', false);
        return $model;
    }
}
