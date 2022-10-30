<?php

namespace App\Containers;

use App\ValueObjects\DelegationDayValueObject;
use Iterator;

class DelegationDaysContainer implements Iterator
{
    private $position = 0;
    private $array = [];

    public function add(DelegationDayValueObject $delegationDayValueObject)
    {
        $this->array[] = $delegationDayValueObject;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->array[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }
}
