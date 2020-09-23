# events

[![Build Status](https://travis-ci.org/DocDoc-team/events.svg?branch=master)](https://travis-ci.org/DocDoc-team/events)
[![Code Coverage](https://scrutinizer-ci.com/g/DocDoc-team/events/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/DocDoc-team/events/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DocDoc-team/events/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/DocDoc-team/events/?branch=master)

## EventEmitter
Очень простая и быстрая реализация событий и фильтров для хуков.

## Installation

```$ composer require docdoc/events```

## Usage

### Creating an Emitter
```php
$emitter = new DocDoc\Event\EventEmitter;
```

### Adding Listeners

Callable handler
```php
$emitter->on('user.created', function (User $user) use ($logger) {
    $logger->log(sprintf("User '%s' was created.", $user->getLogin()));
});
```

With priority
```php
$emitter->on('user.created', $handler, 100);
```

With extra arguments
```php
$extra1 = 1;
$extra2 = 'some';
$emitter->on('log', function(string $log, int $extra1, string $extra2) {
   // ...
}, 50, [$extra1, $extra2]);
$emitter->emit('log', ['some log']);
```


### Removing Listeners

Remove concrete handler
```php
$handler = fn() => 'log';
$emitter->on('log', $handler)
$emitter->off('log', $handler);
```

Remove all handlers
```php
$emitter->off('log');
```

Remove concrete handler with priority
```php
$emitter->off('log', $handler, 100);
```


## Emitting Events

Simple emit
```php
$emitter->emit('log');
```

Pass arguments on emit
```php
$emitter->on('log', function(string $log, int $a2, bool $a3) {
    // ...
});
$emitter->emit('log', ['arg1', 2, true]);
```




## Interface


```php
emit(string $name, array $args = []): self;
on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
off(string $event, callable $handler = null, int $priority = null): self;

listeners(string $event = null): array;
eventNames(): array;
 ```

#### EventHandler
EventHandler must be `callable`

```php
use DocDoc\Event\Events;
use DocDoc\Event\EventEmitter;

$eventsBus = new EventEmitter;

$eventsBus->on('some:event', function(){ ... });
$eventsBus->on('some:event', 'trim');
$eventsBus->on('some', ['ClassName', 'method']);
$eventsBus->on('some', [$this, 'method']);
$eventsBus->once('some', $instanceWithInvokeMethod);
```

#### Order handlers
Listeners has priority. All listeners invoke by priority

```php
$events->on('post:title', 'trim', 10); // second
$events->on('post:title', 'prepareTitle', 70); // first
```

#### Remove handler
```php
// remove handler 'trim' with priority = 10
$events->on('post:title', 'trim', 10);
$events->off('post:title', 'trim', 10);

// remove all handler 'trim' with any priority
$events->on('post:title', 'trim', 10);
$events->off('post:title', 'trim');

// remove all handlers
$events->on('post:title', 'trim', 10);
$events->off('post:title');
```

#### StopPropagation

```php
$events->on('eventName', function(){ ... });
$events->on('eventName', function(){ throw new StopPropagation; });
$events->on('eventName', function(){ ... }); // it does not call
```

## Filters

### API Filters
Filters is very similar to EventEmitter. Filter passes first value through all listeners and returns modified value.

```php
emit(string $name, $value, array $args = []);
on(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
once(string $name, callable $handler, int $priority = 50, array $extraArgs = []): self;
off(string $event, callable $handler = null, int $priority = null): self;

listeners(string $event = null): array;
eventNames(): array;
```

Example
```php
use DocDoc\Event\Filters;

$filters = new Filters;

$filters->on('post:title', 'trim');
$title = $filters->filter('post:title', '   Raw title   '); // `Raw title`
```



### Inject EventEmitter / FilterEmitter

1. Event/Filter Bus.
```php
use DocDoc\Event\EventBusTrait;

class Service {
    use EventBusTrait;

    public function getPost()
    {
        $post = ...;
        // you can to modify $post via filter/event
        $post = $this->filter('getPost', $post); // from EventBusTrait
        return $post;
    }
}
```

2. Event/Filter for any object.

Extend from EventEmitter:
```php
use DocDoc\Event\Filter\FilterEmitter;
use DocDoc\Event\EventEmitter;

class Request extend FilterEmitter { // extend EventEmitter

    public function parseHeader()
    {
        $rawHttpRequest = '...';
        $headers = $this->filter('parseHeader', $rawHttpRequest);
        return $headers;
    }
}

$request = new Request;
$parseHeader = new ParserHeader;
$request->on('parseHeader', [$parseHeader, 'parse']);
$headers = $request->parseHeader();
```

Use trait:

```php
use DocDoc\Event\Filter\FilterEmitterTrait;
use DocDoc\Event\EventEmitterTrait;

class Request

    use FilterEmitterTrait;

    public function parseHeader()
    {
        $rawHttpRequest = '...';
        $headers = $this->filter('parseHeader', $rawHttpRequest);
        return $headers;
    }
}

$request = new Request;
$parseHeader = new ParserHeader;
$request->on('parseHeader', [$parseHeader, 'parse']);
$headers = $request->parseHeader();
```