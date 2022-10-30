<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Response;

class WorkerController extends Controller
{
    /**
     * Store new worker
     *
     */
    public function store()
    {
        $worker = Worker::create();

        return response()->json(['id' => $worker->id], Response::HTTP_CREATED);
    }
}
