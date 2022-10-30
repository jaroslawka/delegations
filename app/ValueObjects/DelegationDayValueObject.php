<?php

namespace App\ValueObjects;

use DateTime;
use Exception;

class DelegationDayValueObject
{
    private $start;
    private $end;
    private $dayOfWeek;
    private $hours;

    const SECONDS_IN_DAY = 86400;

    public function __construct(?DateTime $start = null, ?DateTime $end = null)
    {
        if ($start === null && $end === null) {
            throw new Exception('required at least one DateTime');
        }

        if ($start === null) {
            $start = $this->theDayMidnight($end);
        }

        if ($end === null) {
            $end = $this->theNextDayMidnight($start);
        } else {
            if ($end->getTimestamp() > $this->theNextDayMidnight($start)->getTimestamp()) {
                throw new Exception('the end date must be on the same day');
            }
        }

        $this->start = $start;
        $this->end = $end;
        $this->dayOfWeek = $this->dayOfWeek($start);
        $this->hours = $this->hoursBetween($start, $end);
    }

    protected function dayOfWeek(DateTime $dateTime): int
    {
        return $dateTime->format('N');
    }

    protected function hoursBetween(DateTime $start, DateTime $end): int
    {
        $hours = $end->diff($start)->h;
        return $hours == 0 ? 24 : $hours;
    }

    protected function theDayMidnight(DateTime $dateTime): DateTime
    {
        return new DateTime(date('Y-m-d 00:00:00', $dateTime->getTimestamp()));
    }

    protected function theNextDayMidnight(DateTime $dateTime): DateTime
    {
        return new DateTime(date('Y-m-d 00:00:00', $dateTime->getTimestamp() + self::SECONDS_IN_DAY));
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    public function getHours()
    {
        return $this->hours;
    }

    public function getAllAsString()
    {
        $start = $this->getStart()->format('Y-m-d H:i:s');
        $end = $this->getEnd()->format('Y-m-d H:i:s');
        $dayOfWeek = $this->getDayOfWeek();
        $hours = $this->getHours();

        return "$start $end $dayOfWeek $hours";
    }
}
