<?php

namespace App\Services;

use App\Containers\DelegationDaysContainer;
use App\Models\Delegation;
use App\ValueObjects\DelegationDayValueObject;
use DateTime;
use App\Services\Modules\DelegationAmountDueCalculator;

class DelegationService
{
    private $amountDueCalculator;

    public function __construct(DelegationAmountDueCalculator $amountDueCalculator)
    {
        $this->amountDueCalculator = $amountDueCalculator;
    }

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

        return $this->amountDueCalculator->calculate($baseAmount, $delegationDays);
    }
}

