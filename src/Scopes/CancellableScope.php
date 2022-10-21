<?php

namespace LaravelCancellable\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CancellableScope implements Scope
{
    /**
     * All the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['Cancel', 'UnCancel', 'WithCancelled', 'WithoutCancelled', 'OnlyCancelled'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (is_callable([$model, 'getQualifiedCancelledAtColumn'], true, $name)) {
            $builder->whereNull($model->getQualifiedCancelledAtColumn());
        }
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param Builder $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Get the "cancelled at" column for the builder.
     *
     * @param Builder $builder
     * @return string
     */
    protected function getCancelledAtColumn(Builder $builder)
    {
        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedCancelledAtColumn();
        }

        return $builder->getModel()->getCancelledAtColumn();
    }

    /**
     * Add the cancel extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addCancel(Builder $builder)
    {
        $builder->macro('cancel', function (Builder $builder) {
            $column = $this->getCancelledAtColumn($builder);

            return $builder->update([
                $column => $builder->getModel()->freshTimestampString(),
            ]);
        });
    }

    /**
     * Add the un-cancel extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addUnCancel(Builder $builder)
    {
        $builder->macro('unCancel', function (Builder $builder) {
            $builder->withCancelled();

            $column = $this->getCancelledAtColumn($builder);

            return $builder->update([
                $column => null,
            ]);
        });
    }

    /**
     * Add the with-cancel extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addWithCancelled(Builder $builder)
    {
        $builder->macro('withCancelled', function (Builder $builder, $withCancelled = true) {
            if (! $withCancelled) {
                return $builder->withoutCancelled();
            }

            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-cancel extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addWithoutCancelled(Builder $builder)
    {
        $builder->macro('withoutCancelled', function (Builder $builder) {
            $model = $builder->getModel();

            return $builder->withoutGlobalScope($this)->whereNull(
                $model->getQualifiedCancelledAtColumn()
            );
        });
    }

    /**
     * Add the only-cancel extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addOnlyCancelled(Builder $builder)
    {
        $builder->macro('onlyCancelled', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNotNull(
                $model->getQualifiedCancelledAtColumn()
            );

            return $builder;
        });
    }
}
