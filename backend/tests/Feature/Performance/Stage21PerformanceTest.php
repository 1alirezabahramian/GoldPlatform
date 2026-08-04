<?php

namespace Tests\Feature\Performance;

use App\Http\Controllers\Api\OperatorPanelController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class Stage21PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_order_queue_stays_within_query_budget(): void
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        app(OperatorPanelController::class)->orderQueue();

        $this->assertLessThanOrEqual(2, count(DB::getQueryLog()), 'Order queue exceeded its two-query pagination budget.');
    }

    public function test_operator_delivery_queue_stays_within_query_budget(): void
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        app(OperatorPanelController::class)->deliveryQueue();

        $this->assertLessThanOrEqual(2, count(DB::getQueryLog()), 'Delivery queue exceeded its two-query pagination budget.');
    }

    public function test_stage_21_indexes_exist_on_hot_read_paths(): void
    {
        $expected = [
            'orders' => ['orders_user_status_id_idx', 'orders_status_id_idx'],
            'custody_assets' => ['custody_assets_user_id_idx'],
            'delivery_requests' => ['delivery_requests_user_status_id_idx', 'delivery_requests_status_id_idx'],
        ];

        foreach ($expected as $table => $names) {
            $actual = collect(Schema::getIndexes($table))->pluck('name')->all();

            foreach ($names as $name) {
                $this->assertContains($name, $actual, "Missing performance index {$name} on {$table}.");
            }
        }
    }
}
