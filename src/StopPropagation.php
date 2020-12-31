<?php
declare(strict_types=1);

namespace DocDoc\Event;

use Exception;

class StopPropagation extends Exception
{
    /** @var mixed */
    public $value;

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}
