<?php

namespace Tests\Feature;

use App\Models\LateDisciplineRule;
use App\Service\LateDisciplineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LateDisciplineServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_rule_is_not_used_for_calculation(): void
    {
        LateDisciplineRule::create([
            'name' => 'Rule Inactive',
            'threshold' => 3,
            'period_type' => 'monthly',
            'action_type' => 'payroll_deduction',
            'action_amount' => 20000,
            'status' => 'inactive',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Aturan keterlambatan aktif belum dikonfigurasi.'
        );

        app(LateDisciplineService::class)->calculateFromStatuses(
            $this->lateStatuses(3)
        );
    }

    public function test_multiple_active_rules_make_calculation_explicitly_ambiguous(): void
    {
        $this->createRule('Rule A');
        $this->createRule('Rule B');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Terdapat lebih dari satu aturan keterlambatan aktif.'
        );

        app(LateDisciplineService::class)->calculateFromStatuses(
            $this->lateStatuses(3)
        );
    }

    public function test_late_discipline_uses_monthly_multiples_and_keeps_months_separate(): void
    {
        $rule = $this->createRule();

        $statuses = collect([
            $this->state('2026-09-01'),
            $this->state('2026-09-02'),
            $this->state('2026-09-03'),
            $this->state('2026-09-04'),
            $this->state('2026-09-05'),
            $this->state('2026-09-06'),
            $this->state('2026-10-01'),
            $this->state('2026-10-02'),
            $this->state('2026-10-03'),
        ]);

        $result = app(LateDisciplineService::class)->calculateFromStatuses(
            statuses: $statuses,
            rule: $rule,
        );

        $this->assertSame(9, $result['late_count']);
        $this->assertSame(3, $result['deduction_units']);
        $this->assertSame(60000.0, $result['deduction_amount']);

        $this->assertCount(2, $result['items']);
        $this->assertSame('2026-09', $result['items'][0]['month']);
        $this->assertSame(2, $result['items'][0]['deduction_units']);
        $this->assertSame(40000.0, $result['items'][0]['amount']);

        $this->assertSame('2026-10', $result['items'][1]['month']);
        $this->assertSame(1, $result['items'][1]['deduction_units']);
        $this->assertSame(20000.0, $result['items'][1]['amount']);
    }

    public function test_below_threshold_does_not_create_deduction(): void
    {
        $rule = $this->createRule();

        $result = app(LateDisciplineService::class)->calculateFromStatuses(
            statuses: $this->lateStatuses(2),
            rule: $rule,
        );

        $this->assertSame(2, $result['late_count']);
        $this->assertSame(0, $result['deduction_units']);
        $this->assertSame(0.0, $result['deduction_amount']);
        $this->assertCount(0, $result['items']);
    }

    public function test_unsupported_period_type_is_rejected(): void
    {
        $rule = $this->createRule([
            'period_type' => 'weekly',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Periode aturan keterlambatan saat ini harus bulanan.'
        );

        app(LateDisciplineService::class)->calculateFromStatuses(
            $this->lateStatuses(3),
            $rule,
        );
    }

    public function test_unsupported_action_type_is_rejected(): void
    {
        $rule = $this->createRule([
            'action_type' => 'warning',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Konsekuensi aturan keterlambatan saat ini harus berupa potongan gaji.'
        );

        app(LateDisciplineService::class)->calculateFromStatuses(
            $this->lateStatuses(3),
            $rule,
        );
    }

    public function test_explicitly_passed_rule_must_be_active(): void
    {
        $rule = $this->createRule([
            'status' => 'inactive',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Aturan keterlambatan yang digunakan harus berstatus aktif.'
        );

        app(LateDisciplineService::class)->calculateAmountFromCount(
            lateCount: 3,
            rule: $rule,
        );
    }

    /**
     * @param array<string,mixed> $overrides
     */
    private function createRule(array $overrides = []): LateDisciplineRule
    {
        return LateDisciplineRule::create(array_merge([
            'name' => 'Potongan Keterlambatan',
            'threshold' => 3,
            'period_type' => 'monthly',
            'action_type' => 'payroll_deduction',
            'action_amount' => 20000,
            'status' => 'active',
        ], $overrides));
    }

    private function lateStatuses(int $count): Collection
    {
        return collect(range(1, $count))
            ->map(
                fn(int $day) => $this->state(
                    sprintf('2026-09-%02d', $day)
                )
            );
    }

    /**
     * @return array<string,mixed>
     */
    private function state(string $date): array
    {
        return [
            'date' => $date,
            'is_late' => true,
        ];
    }
}
