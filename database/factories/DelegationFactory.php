<?php

namespace Database\Factories;

use App\Models\Delegation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DelegationFactory extends Factory
{
    protected $model = Delegation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'id' => 1,
            'worker_id' => 1,
            'start' => '2000-00-00 00:00:00',
            'end' => '2000-00-00 00:00:00',
            'country' => 'PL',
        ];
    }
}
