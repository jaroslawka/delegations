<?php

namespace App\Http\Controllers;

use App\Helpers\Iso3166Alpha2;
use App\Http\Requests\DelegationRequest;
use App\Models\Allowance;
use App\Models\Delegation;
use Illuminate\Http\Response;

class DelegationController extends Controller
{

    protected $iso3166Alpha2;

    public function __construct(Iso3166Alpha2 $iso3166Alpha2)
    {
        $this->iso3166Alpha2 = $iso3166Alpha2;
    }

    /**
     * Store new delegation
     *
     */
    public function store(DelegationRequest $delegationRequest)
    {
        $validatedRequest = $delegationRequest->validated();

        $errorMsg = $this->delegationValidationRules($validatedRequest);

        if ($errorMsg) {
            return response(['error' => true, 'error-msg' => $errorMsg], Response::HTTP_BAD_REQUEST);
        }

        Delegation::create([
            'worker_id' => $validatedRequest['worker_id'],
            'start' => $validatedRequest['start'],
            'end' => $validatedRequest['end'],
            'country' => $validatedRequest['country']
        ]);

        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * Delegation business rules
     *
     */
    protected function delegationValidationRules(array $data)
    {
        // The end date cannot be greater than the start date
        if (strtotime($data['start']) > strtotime($data['end'])) {
            return 'The end date cannot be greater than the start date';
        }

        // ISO 3316-1-Alpha2 proper code
        if (!$this->iso3166Alpha2->isCountryCode($data['country'])) {
            return 'Invalid country code';
        }

        // Delegation only for countries in allowances table
        if (Allowance::where('country', $data['country'])->doesntExist()) {
            return 'Delegation only for countries in allowances table';
        }

        // Cannot be added when there is another delegation at the time
        if (Delegation::where('worker_id', $data['worker_id'])
            ->where(function ($query) use ($data) {
                $query->where(function ($query) use ($data) {
                    $query->where('start', '<=', $data['start'])
                        ->where('end', '>=', $data['start']);
                })->orWhere(function ($query) use ($data) {
                    $query->where('start', '<=', $data['end'])
                        ->where('end', '>=', $data['end']);
                });
            })->exists()) {
            return 'Cannot be added when there is another delegation at the time';
        }
    }

}
