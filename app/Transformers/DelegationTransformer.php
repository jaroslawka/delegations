<?php

namespace App\Transformers;

use App\Models\Delegation;
use App\Services\DelegationService;

class DelegationTransformer
{
    private $delegationService;

    public function __construct(DelegationService $delegationService)
    {
        $this->delegationService = $delegationService;
    }

    public function transform(Delegation $delegation): Delegation
    {
        $delegation->amount_due = $this->delegationService->calculateAmountDue($delegation);
        $delegation->currency = 'PLN'; // FIXME delegation hasOne Currency

        return $delegation;
    }
}
