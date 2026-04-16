<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Documents the sort contract used by {@see \Botble\CarRentals\Services\TripTimelineBuilder::finalizeTimelineRows}.
 * If ordering rules change, update this test and the builder together.
 */
class TripTimelineSortContractTest extends TestCase
{
    public function test_occurred_at_then_source_tiebreak(): void
    {
        $rows = [
            ['occurred_at' => '2026-04-12T10:00:00+00:00', 'source' => 'b'],
            ['occurred_at' => '2026-04-10T08:00:00+00:00', 'source' => 'z'],
            ['occurred_at' => '2026-04-10T08:00:00+00:00', 'source' => 'a'],
        ];

        usort($rows, function (array $a, array $b): int {
            return strcmp($a['occurred_at'], $b['occurred_at']) ?: strcmp($a['source'], $b['source']);
        });

        $this->assertSame(['a', 'z', 'b'], array_column($rows, 'source'));
    }
}
