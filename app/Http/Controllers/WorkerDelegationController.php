<?php

namespace App\Http\Controllers;

use App\Transformers\DelegationTransformer;
use App\Models\Worker;
use Illuminate\Http\Response;

class WorkerDelegationController extends Controller
{

    function index($id, DelegationTransformer $transformer)
    {
        $worker = Worker::find($id);

        if ($worker === null) {
            return response(['error' => true, 'error-msg' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $workerDelegations = $worker->delegations()->with('allowance')->select('start', 'end', 'country')->get();

        $workerDelegations->transform(function ($item) use ($transformer) {
            $item = $transformer->transform($item);
            unset($item->allowance);
            return $item;
        });

        return response()->json($workerDelegations, Response::HTTP_OK);
    }
}
