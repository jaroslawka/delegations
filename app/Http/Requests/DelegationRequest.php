<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class DelegationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'start' => 'required|date',
            'end' => 'required|date',
            'worker_id' => 'required|integer',
            'country' => 'required|regex:/[A-Z][A-Z]/'
        ];
    }
}
