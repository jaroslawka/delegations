<?php

namespace App\Services\Modules;

use App\ValueObjects\DelegationDayValueObject;
use App\Containers\DelegationDaysContainer;

class DelegationAmountDueCalculator
{
    private $daysNoAllowance = [6, 7]; // allowance not paid for days of the week in ISO 8601 numeric representation
    private $hoursForPayedDays = 8; // minimum number of hours in day for payed allowance
    private $daysAfterDoubledAmount = 7; // number of days after that allowance is doubled

    protected function rulesDaysNoAllowance(DelegationDayValueObject $delegationDay): bool
    {
        // when the day (daysNoAllowance) is Saturday and Sundays no allowance
        if (in_array($delegationDay->getDayOfWeek(), $this->daysNoAllowance)) {
            return true;
        }

        return false;
    }

    protected function amountForDay(int $baseAmount, int $calendarDayNumber, DelegationDayValueObject $delegationDay): int
    {
        $amount = 0;

        // the allowance only when the number of hours is at least 8 (hoursForPayedDays)
        if ($delegationDay->getHours() >= $this->hoursForPayedDays) {

            // when the allowance is longer than 7 (daysAfterDoubledAmount) days - for next day allowance amount is doubled
            $amount = ($calendarDayNumber <= $this->daysAfterDoubledAmount ? $baseAmount : $baseAmount * 2);
        }

        return $amount;
    }

    public function calculate(int $baseAmount, DelegationDaysContainer $delegationDaysContainer): int
    {
        $amount=0;
        $calendarDayNumber = 0;

        foreach ($delegationDaysContainer AS $delegationDay) {
            $calendarDayNumber++;

            if ($this->rulesDaysNoAllowance($delegationDay)) {
                continue;
            }

            $amount += $this->amountForDay($baseAmount, $calendarDayNumber, $delegationDay);
        }

        return $amount;
    }
}
