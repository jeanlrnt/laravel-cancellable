<?php

namespace LaravelCancellable;

use Exception;
use LaravelCancellable\Scopes\CancellableScope;

/**
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withCancelled()
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder onlyCancelled()
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withoutCancelled()
 */
trait Cancellable
{
    /**
     * Indicates if the model should use cancels.
     *
     * @var bool
     */
    public $cancels = true;

    /**
     * Boot the cancelling trait for a model.
     *
     * @return void
     */
    public static function bootCancellable()
    {
        static::addGlobalScope(new CancellableScope);
    }

    /**
     * Initialize the cancellable trait for an instance.
     *
     * @return void
     */
    public function initializeCancellable()
    {
        if (! isset($this->casts[$this->getCancelledAtColumn()])) {
            $this->casts[$this->getCancelledAtColumn()] = 'datetime';
        }
    }

    /**
     * Cancel the model.
     *
     * @return bool|null
     *
     * @throws Exception
     */
    public function cancel()
    {
        $this->mergeAttributesFromClassCasts();

        if (is_null($this->getKeyName())) {
            throw new Exception('No primary key defined on model.');
        }

        // If the model doesn't exist, there is nothing to cancel.
        if (! $this->exists) {
            return;
        }

        // If the cancelling event doesn't return false, we'll continue
        // with the operation.
        if ($this->fireModelEvent('cancelling') === false) {
            return false;
        }

        // Update the timestamps for each of the models owners. Breaking any caching
        // on the parents
        $this->touchOwners();

        $this->runCancel();

        // Fire cancelled event to allow hooking into the post-cancel operations.
        $this->fireModelEvent('cancelled', false);

        // Return true as the cancel is presumably successful.
        return true;
    }

    /**
     * Perform the actual cancel query on this model instance.
     *
     * @return void
     */
    public function runCancel()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [$this->getCancelledAtColumn() => $this->fromDateTime($time)];

        $this->{$this->getCancelledAtColumn()} = $time;

        if ($this->usesTimestamps() && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));
    }

    public function unCancel()
    {
        // If the cancelling event return false, we will exit the operation.
        // Otherwise, we will clear the cancelled at timestamp and continue
        // with the operation
        if ($this->fireModelEvent('unCancelling') === false) {
            return false;
        }

        $this->{$this->getCancelledAtColumn()} = null;

        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('unCancelled', false);

        return $result;
    }

    /**
     * Determine if the model instance has been cancelled.
     *
     * @return bool
     */
    public function isCancelled()
    {
        return ! is_null($this->{$this->getCancelledAtColumn()});
    }

    /**
     * Register a "cancelling" model event callback with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function cancelling($callback)
    {
        static::registerModelEvent('cancelling', $callback);
    }

    /**
     * Register a "cancelled" model event callback with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function cancelled($callback)
    {
        static::registerModelEvent('cancelled', $callback);
    }

    /**
     * Register a "un-cancelling" model event callback with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function unCancelling($callback)
    {
        static::registerModelEvent('unCancelling', $callback);
    }

    /**
     * Register a "un-cancelled" model event callback with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function unCancelled($callback)
    {
        static::registerModelEvent('unCancelled', $callback);
    }

    /**
     * Get the name of the "cancelled at" column.
     *
     * @return string
     */
    public function getCancelledAtColumn()
    {
        return defined('static::CANCELLED_AT') ? static::CANCELLED_AT : 'cancelled_at';
    }

    /**
     * Get the fully qualified "cancelled at" column.
     *
     * @return string
     */
    public function getQualifiedCancelledAtColumn()
    {
        return $this->qualifyColumn($this->getCancelledAtColumn());
    }
}