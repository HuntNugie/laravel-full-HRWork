<?php

namespace App\Service;

use App\Models\EmployeeWarningLetter;
use App\Models\WarningLetterSequence;
use Illuminate\Support\Facades\DB;

class WarningLetterService
{
    public function previewWarningLetterNumber(): string
    {
        $year = now()->year;
        $month = now()->month;

        $lastNumber = WarningLetterSequence::query()
            ->where('year', $year)
            ->where('month', $month)
            ->value('last_number') ?? 0;

        return $this->formatWarningLetterNumber(
            $year,
            $month,
            $lastNumber + 1
        );
    }

    public function generateWarningLetterNumber(): string
    {
        return DB::transaction(function (): string {
            $year = now()->year;
            $month = now()->month;

            $sequence = WarningLetterSequence::query()
                ->where('year', $year)
                ->where('month', $month)
                ->lockForUpdate()
                ->first();

            if (!$sequence) {
                WarningLetterSequence::create([
                    'year' => $year,
                    'month' => $month,
                    'last_number' => 1,
                ]);

                return $this->formatWarningLetterNumber(
                    $year,
                    $month,
                    1
                );
            }

            $sequence->increment('last_number');
            $sequence->refresh();

            return $this->formatWarningLetterNumber(
                $year,
                $month,
                $sequence->last_number
            );
        });
    }

    /**
     * Create a Warning Letter in draft state.
     *
     * Number generation and draft creation happen in one transaction so a
     * successfully created draft always has a reserved unique letter number.
     *
     * @param array{
     *   employee_id:int,
     *   warning_level:string,
     *   issued_date:string,
     *   reason:string,
     *   description?:string|null
     * } $attributes
     */
    public function createDraft(
        array $attributes,
        int $createdBy,
    ): EmployeeWarningLetter {
        return DB::transaction(function () use (
            $attributes,
            $createdBy
        ): EmployeeWarningLetter {
            $letterNumber = $this->generateWarningLetterNumber();

            return EmployeeWarningLetter::query()->create([
                'employee_id' => $attributes['employee_id'],
                'warning_level' => $attributes['warning_level'],
                'letter_number' => $letterNumber,
                'issued_date' => $attributes['issued_date'],
                'reason' => trim($attributes['reason']),
                'description' => filled($attributes['description'] ?? null)
                    ? trim($attributes['description'])
                    : null,
                'status' => 'draft',
                'created_by' => $createdBy,
            ]);
        });
    }

    /**
     * Update an existing Warning Letter draft.
     *
     * The row is locked and its state is checked inside the transaction so two
     * concurrent requests cannot edit a letter after it has been issued/cancelled.
     *
     * @param array{
     *   employee_id:int,
     *   warning_level:string,
     *   issued_date:string,
     *   reason:string,
     *   description?:string|null
     * } $attributes
     */
    public function updateDraft(
        EmployeeWarningLetter $warningLetter,
        array $attributes,
    ): EmployeeWarningLetter {
        return DB::transaction(function () use (
            $warningLetter,
            $attributes
        ): EmployeeWarningLetter {
            $locked = EmployeeWarningLetter::query()
                ->whereKey($warningLetter->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureDraft($locked);

            $locked->update([
                'employee_id' => $attributes['employee_id'],
                'warning_level' => $attributes['warning_level'],
                'issued_date' => $attributes['issued_date'],
                'reason' => trim($attributes['reason']),
                'description' => filled($attributes['description'] ?? null)
                    ? trim($attributes['description'])
                    : null,
            ]);

            return $locked->refresh();
        });
    }

    /**
     * Issue a Warning Letter draft.
     */
    public function issue(
        EmployeeWarningLetter $warningLetter,
        int $issuedBy,
    ): EmployeeWarningLetter {
        return DB::transaction(function () use (
            $warningLetter,
            $issuedBy
        ): EmployeeWarningLetter {
            $locked = EmployeeWarningLetter::query()
                ->whereKey($warningLetter->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureDraft($locked);

            if (!$locked->letter_number || !$locked->issued_date) {
                throw new \RuntimeException(
                    'Surat Peringatan belum memiliki nomor dan tanggal surat.'
                );
            }

            if (blank($locked->reason)) {
                throw new \RuntimeException(
                    'Alasan Surat Peringatan wajib diisi sebelum diterbitkan.'
                );
            }

            $locked->update([
                'status' => 'issued',
                'issued_by' => $issuedBy,
                'issued_at' => now(),
            ]);

            return $locked->refresh();
        });
    }

    /**
     * Cancel a draft or issued Warning Letter.
     */
    public function cancel(
        EmployeeWarningLetter $warningLetter,
        string $reason,
        int $cancelledBy,
    ): EmployeeWarningLetter {
        return DB::transaction(function () use (
            $warningLetter,
            $reason,
            $cancelledBy
        ): EmployeeWarningLetter {
            $locked = EmployeeWarningLetter::query()
                ->whereKey($warningLetter->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($locked->status, ['draft', 'issued'], true)) {
                throw new \RuntimeException(
                    'Surat Peringatan yang sudah dibatalkan tidak dapat dibatalkan kembali.'
                );
            }

            $reason = trim($reason);

            if ($reason === '') {
                throw new \RuntimeException(
                    'Alasan pembatalan wajib diisi.'
                );
            }

            $locked->update([
                'status' => 'cancelled',
                'cancelled_by' => $cancelledBy,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            return $locked->refresh();
        });
    }

    private function ensureDraft(EmployeeWarningLetter $warningLetter): void
    {
        if ($warningLetter->status !== 'draft') {
            throw new \RuntimeException(
                'Surat Peringatan sudah tidak dapat diedit atau diterbitkan.'
            );
        }
    }

    private function formatWarningLetterNumber(
        int $year,
        int $month,
        int $number
    ): string {
        return 'SP/' .
            $year . '/' .
            str_pad($month, 2, '0', STR_PAD_LEFT) . '/' .
            str_pad($number, 3, '0', STR_PAD_LEFT);
    }
}
