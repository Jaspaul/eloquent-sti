<?php

namespace Jaspaul\EloquentSTI;

use Jaspaul\EloquentSTI\Exceptions\UndefinedTypeException;

trait StrictTypes
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::addGlobalScope(new TypeScope());

        self::saving(function ($model) {
            $model->setTypeValue();
        });
    }

    /**
     * Returns the expected type value for the object.
     *
     * @return string
     */
    public function getTypeValue(): string
    {
        $flipped = $this->getTypes()->flip();

        if (!$flipped->has(static::class)) {
            throw new UndefinedTypeException(sprintf(
                'Looks like "%s" has not been defined in your types map in "%s".',
                static::class,
                self::class
            ));
        }

        return $flipped->get(static::class);
    }

    /**
     * Returns the value for the type column.
     *
     * @return void
     */
    private function setTypeValue(): void
    {
        $this->{$this->getTypeColumn()} = $this->getTypeValue();
    }
}
