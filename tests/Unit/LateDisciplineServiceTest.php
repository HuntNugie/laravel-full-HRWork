<?php

namespace Tests\Unit;

use App\Models\LateDisciplineRule;
use App\Service\LateDisciplineService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class LateDisciplineServiceTest extends TestCase
{
    public function test_three_late_events_create_one_deduction_unit(): void
    {
        $service = app(LateDisciplineService::class);

        $result = $service->calculateFromStatuses(
            statuses: collect([
                $this->lateState('2026-09-01'),
                $this->lateState('2026-09-10'),
                $this->lateState('2026-09-20'),
            ]),
            rule: $this->rule(),
        );

        $this->assertSame(3, $result['late_count']);
        $this->assertSame(1, $result['deduction_units']);
        $this->assertSame(20000.0, $result['deduction_amount']);
        $this->assertCount(1, $result['items']);
    }

    public function test_six_late_events_create_two_deduction_units_in_one_month(): void
    {
        $service = app(LateDisciplineService::class);

        $result = $service->calculateFromStatuses(
            statuses: collect([
                $this->lateState('2026-09-01'),
                $this->lateState('2026-09-02'),
                $this->lateState('2026-09-03'),
                $this->lateState('2026-09-10'),
                $this->lateState('2026-09-11'),
                $this->lateState('2026-09-12'),
            ]),
            rule: $this->rule(),
        );

        $this->assertSame(6, $result['late_count']);
        $this->assertSame(2, $result['deduction_units']);
        $this->assertSame(40000.0, $result['deduction_amount']);
    }

    public function test_late_events_are_counted_separately_per_month(): void
    {
        $service = app(LateDisciplineService::class);

        $result = $service->calculateFromStatuses(
            statuses: collect([
                $this->lateState('2026-09-01'),
                $this->lateState('2026-09-02'),
                $this->lateState('2026-09-03'),
                $this->lateState('2026-10-01'),
                $this->lateState('2026-10-02'),
                $this->lateState('2026-10-03'),
            ]),
            rule: $this->rule(),
        );

        $this->assertSame(6, $result['late_count']);
        $this->assertSame(2, $result['deduction_units']);
        $this->assertSame(40000.0, $result['deduction_amount']);
        $this->assertCount(2, $result['items']);
    }

    public function test_non_late_states_do_not_create_discipline(): void
    {
        $service = app(LateDisciplineService::class);

        $result = $service->calculateFromStatuses(
            statuses: collect([
                $this->lateState('2026-09-01'),
                $this->state('2026-09-02', false),
                $this->lateState('2026-09-03'),
            ]),
            rule: $this->rule(),
        );

        $this->assertSame(2, $result['late_count']);
        $this->assertSame(0, $result['deduction_units']);
        $this->assertSame(0.0, $result['deduction_amount']);
        $this->assertCount(0, $result['items']);
    }

    private function rule(): LateDisciplineRule
    {
        return new LateDisciplineRule([
            'threshold' => 3,
            'action_amount' => 20000,
            'period_type' => 'monthly',
            'action_type' => 'payroll_deduction',
        ]);
    }

    private function lateState(string $date): array
    {
        return $this->state($date, true);
    }

    private function state(string $date, bool $isLate): array
    {
        return [
            'date' => $date,
            'is_late' => $isLate,
            'is_paid' => true,
            'is_working_day' => true,
        ];
    }
}
