<?php

class Event
{
    
    /**
     * Registered events.
     * 
     * @var array $events
     */
    protected $events = [];

    /**
     * List of fired events.
     * 
     * @var array $firing
     */
    protected $fired = [];

    /**
     * Events sorted by priority.
     * 
     * @var array $sorted
     */
    protected $sorted = [];

    /**
     * Wildcard events.
     * 
     * @var array $wildcards
     */
    protected $wildcards = [];

    /**
     * Determine if an event exists.
     * 
     * @param string $event Name of the event.
     * @return bool
     */
    public function exists($event)
    {
        return isset($this->events[$event]);
    }

    /**
     * Fire an event.
     * 
     * @param string $event Name of the event.
     * @param array $parameters Parameters to pass to callback.
     * @param bool $halt Stop execution after success?
     * @return array|null
     */
    public function fire($event, array $parameters = [], $halt = false)
    {
        $responses = [];

        $this->fired[] = [
            'event' => $event,
            'parameters' => $parameters
        ];

        $listeners = $this->getListeners($event);
        foreach ($listeners as $listener) {
            $response = call_user_func_array($listener, $parameters);

            if (null !== $response && $halt) {
                array_pop($this->fired);

                return $response;
            }

            if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        array_pop($this->fired);

        return $halt ? null : $responses;
    }

    /**
     * Get the firing event.
     * 
     * @return string
     */
    public function firing()
    {
        return end($this->fired);
    }

    /**
     * Get an array of event listeners for an event.
     * 
     * @param string $event Name of the event.
     * @return array
     */
    public function getListeners($event)
    {
        $wildcards = $this->getWildcardListeners($event);

        if (!isset($this->sorted[$event])) {
            $this->sortListeners($event);
        }

        return array_merge($this->sorted[$event], $wildcards);
    }

    /**
     * Register an event listener.
     * 
     * @param string $event Name of the event.
     * @param callable $callback Callback to fire.
     * @param int $priority Event priority.
     * @return self
     */
    public function addListener($event, callable $callback, $priority = 0)
    {
        if (false !== strpos($event, '*')) {
            $this->wildcards[$event][] = $callback;
        } else {
            $this->events[$event][$priority][] = $callback;
        }

        return $this;
    }

    /**
     * Remove an events listeners.
     * 
     * @param string $event Name of the event.
     * @return self
     */
    public function removeListeners($event)
    {
        unset($this->events[$event]);
        unset($this->sorted[$event]);

        return $this;
    }

    /**
     * Fire an event until successful.
     * 
     * @param string $event Name of the event.
     * @param array $parameters Parameters to pass to callback.
     * @return null|array
     */
    public function until($event, array $parameters = [])
    {
        return $this->fire($event, $parameters, true);
    }

    /**
     * Get an array of wildcard listeners for an event.
     * 
     * @param string $event Name of the event.
     * @return array
     */
    protected function getWildcardListeners($event)
    {
        $wildcards = [];

        foreach ($this->wildcards as $key => $listeners) {
            if ($key === $event) {
                $wildcards = array_merge($wildcards, $listeners);
            }
        }

        return $wildcards;
    }

    /**
     * Sort an events listeners by priority.
     * 
     * @param string $event Name of the event.
     */
    protected function sortListeners($event)
    {
        $this->sorted[$event] = [];

        if (isset($this->events[$event])) {
            krsort($this->events[$event]);

            $this->sorted[$event] = call_user_func_array('array_merge', $this->events[$event]);
        }
    }

}
