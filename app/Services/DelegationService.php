<?php

namespace App\Services;

use App\Containers\DelegationDaysContainer;
use App\Models\Delegation;
use App\ValueObjects\DelegationDayValueObject;
use DateTime;

class DelegationService
{
    private $daysNoAllowance = [6, 7]; // allowance not paid for days of the week in ISO 8601 numeric representation
    private $hoursForPayedDays = 8; // minimum number of day for payed allowance
    private $daysAfterDoubledAmount = 7; // number of days after that allowance is doubled

    protected function getDelegationDays(DateTime $start, DateTime $end): DelegationDaysContainer
    {
        $delegationDaysContainer = new DelegationDaysContainer();

        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
            $delegationDay = new DelegationDayValueObject($start, $end);
            $delegationDaysContainer->add($delegationDay);
            return $delegationDaysContainer;
        }

        $delegationDay = new DelegationDayValueObject($start);
        $delegationDaysContainer->add($delegationDay);

        $daysBetween = $end->diff($start)->days;

        if ($daysBetween > 0) {
            for ($day = 0; $day < $daysBetween; $day++) {
                $delegationDay = new DelegationDayValueObject($delegationDay->getEnd(), null);
                $delegationDaysContainer->add($delegationDay);
            }
        }

        $delegationDaysContainer->add(new DelegationDayValueObject(null, $end));

        return $delegationDaysContainer;
    }

    public function calculateAmountDue(Delegation $delegation): int
    {
        $startDateTime = new DateTime($delegation->start);
        $endDateTime = new DateTime($delegation->end);
        $baseAmount = $delegation->allowance->amount;

        $delegationDays = $this->getDelegationDays($startDateTime, $endDateTime);

        $amount = 0;
        $day = 0;

        foreach ($delegationDays AS $delegationDay) {
            $day++;

            // when the day (daysNoAllowance) is Saturday and Sundays no allowance
            if (in_array($delegationDay->getDayOfWeek(), $this->daysNoAllowance)) {
                continue;
            }

            // the allowance only when the number of hours is at least 8 (hoursForPayedDays)
            if ($delegationDay->getHours() >= $this->hoursForPayedDays) {
                // when the allowance is longer than 7 (daysAfterDoubledAmount) days - for next day allowance amount is doubled
                $amount += ($day <= $this->daysAfterDoubledAmount ? $baseAmount : $baseAmount * 2);
            }
        }

        return $amount;
    }
}