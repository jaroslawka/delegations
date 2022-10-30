<?php

namespace Tests\Feature;

use Tests\TestCase;
use Exception;
use Illuminate\Http\Response;

class EndpointsTest extends TestCase
{
    public function test_worker_store()
    {
        // given: empty data set

        $data = [];

        // when: post request to "api/worker" with data

        $response = $this->post('/api/worker', $data);

        // then: response created with worker id

        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(function ($json) {
                $json->has('id');
            });
    }

    public function test_delegation_store()
    {
        // given: proper delegation data set

        $delegation = [
            'worker_id' => 1,
            'start' => '2022-01-01 08:00:00',
            'end' => '2022-01-02 08:00:00',
            'country' => 'PL'
        ];

        // when: post request to "api/delegation" with delegation data set

        $response = $this->post('/api/delegation', $delegation);

        // then: response created

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_delegation_not_stored_for_the_same_period_of_time()
    {
        // given: proper delegation data set but with the same period of time

        $delegation = [
            'worker_id' => 1,
            'start' => '2022-01-01 08:00:00',
            'end' => '2022-01-02 08:00:00',
            'country' => 'PL'
        ];

        // when: post request to "api/delegation" with delegation data set

        $response = $this->post('/api/delegation', $delegation);

        // then: response bad request

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }


    public function test_delegation_not_stored_for_the_country_code_outside_the_allowance_table()
    {
        // given: proper delegation data set but with the code outside the allowance table

        $delegation = [
            'worker_id' => 1,
            'start' => '2022-02-01 08:00:00',
            'end' => '2022-02-02 08:00:00',
            'country' => 'FR'
        ];

        // when: post request to "api/delegation" with delegation data set

        $response = $this->post('/api/delegation', $delegation);

        // then: response bad request

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_delegation_not_stored_for_the_start_date_after_end_date()
    {
        // given: proper delegation data set but with the start date after end date

        $delegation = [
            'worker_id' => 1,
            'start' => '2022-03-03 08:00:00',
            'end' => '2022-03-02 08:00:00',
            'country' => 'PL'
        ];

        // when: post request to "api/delegation" with delegation data set

        $response = $this->post('/api/delegation', $delegation);

        // then: response bad request

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }



    public function test_delegation_not_stored_for_the_country_code_does_not_meet_the_iso_standard()
    {
        // given: proper delegation data set but with the code does not meet the iso standard

        $delegation = [
            'worker_id' => 1,
            'start' => '2022-04-01 08:00:00',
            'end' => '2022-04-03 08:00:00',
            'country' => 'XX'
        ];

        // when: post request to "api/delegation" with delegation data set

        $response = $this->post('/api/delegation', $delegation);

        // then: response bad request

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_delegations_list()
    {
        // given: id user 1

        $id = 1;

        // when: post request to "api/worker/{id}/delegations" with id user

        $response = $this->get('/api/worker/' . $id . '/delegations');

        /* then: response contain JSON:

        [
            {
                "start": "2020-04-20 08:00:00",
                "end": "2020-04-21 16:00:00",
                "country": "PL",
                "amount_due": 20,
                "currency": "PLN"
            },
            {
                "start": "2020-04-24 20:00:00",
                "end": "2020-04-28 16:00:00",
                "country": "DE",
                "amount_due": 150,
                "currency": "PLN"
            }
        ]

        */

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(function ($json) {
                foreach ($json->toArray() AS $item) {
                    if (!(bool)strtotime($item['start'])) {
                        throw new Exception();
                    }
                    if (!(bool)strtotime($item['end'])) {
                        throw new Exception();
                    }
                    if (!preg_match('/[A-Z][A-Z]/', $item['country'])) {
                        throw new Exception();
                    }
                    if (!is_integer($item['amount_due'])) {
                        throw new Exception();
                    }
                    if (!is_string($item['currency'])) {
                        throw new Exception();
                    }
                }
            });
    }
}
