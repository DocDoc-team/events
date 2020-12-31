<?php
declare(strict_types=1);

namespace DocDoc\Event;

use DocDoc\Event\Filter\FilterEmitterInterface;

trait EventBusTrait
{
    protected ?EventEmitterInterface $events = null;
    protected ?FilterEmitterInterface $filters = null;

    public function setEvents(EventEmitterInterface $events): void
    {
        $this->events = $events;
    }

    public function setFilters(FilterEmitterInterface $filters): void
    {
        $this->filters = $filters;
    }

    public function filter(string $name, $value, array $arguments = [])
    {
        return $this->filters ? $this->filters->emit($name, $value, $arguments) : $value;
    }

    public function emit(string $name, array $arguments = []): void
    {
        $this->events && $this->events->emit($name, $arguments);
    }
}
