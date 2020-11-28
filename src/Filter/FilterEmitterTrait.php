<?php
declare(strict_types=1);

namespace DocDoc\Event\Filter;

use DocDoc\Event\EventEmitterTrait;
use DocDoc\Event\StopPropagation;

trait FilterEmitterTrait
{
    use EventEmitterTrait;

    public function emit(string $name, mixed $value, array $args = []): mixed
    {
        try {
            foreach ($this->listeners[$name] ?? [] as $i => $listener) {
                $handler = $listener->handler;
                $value = $handler($value, ...$args, ...$listener->extraArgs);

                if ($listener->once) {
                    unset($this->listeners[$name][$i]);
                    if (count($this->listeners[$name]) === 0) {
                        unset($this->listeners[$name]);
                    }
                }
            }
        } catch (StopPropagation $e) {
            return $e->getValue();
        }

        return $value;
    }
}
