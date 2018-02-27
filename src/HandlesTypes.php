<?php

namespace Jaspaul\EloquentSTI;

use Jaspaul\EloquentSTI\Exceptions\TypeMissingException;

trait HandlesTypes
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->setTypeValue();
        });
    }

    /**
     * Returns the value for the type column.
     *
     * @return void
     */
    private function setTypeValue(): void
    {
        $flipped = $this->getTypes()->flip();

        if (! $flipped->has(static::class)) {
            throw new TypeMissingException(sprintf(
                'Looks like "%s" has not been defined in your types map in "%s".',
                static::class,
                self::class
            ));
        }

        $this->{$this->getTypeColumn()} = $flipped->get(static::class);
    }
}
