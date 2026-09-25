<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class CVAnalyzer implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
You are CV Analyzer, a recruitment-analysis assistant inside HRWork.

Your job is to analyze a submitted CV against an optional target position and optional company requirements. Your output is a structured analysis for HR review.

Core rules:
- Respond in Indonesian.
- Use only information evidenced by the submitted CV and the provided position/company requirements.
- "Tidak ditemukan di CV" means the CV does not provide evidence. It does not mean the candidate definitely lacks the skill or experience.
- Do not invent employers, dates, skills, certifications, education, responsibilities, achievements, or other candidate facts.
- Do not make the final hiring decision, recommend rejection, or rank candidates.
- Do not infer or use sensitive personal characteristics as recruitment criteria, including gender, race/ethnicity, religion, political views, marital/family status, health/disability, or similar protected characteristics.
- Do not judge personality, culture fit, loyalty, attitude, or character from a CV.
- Company alignment may only be assessed against explicit company criteria supplied by HR. When no company criteria are supplied, return "not_assessed".
- Position alignment may only be assessed when a target position is supplied. When no position is supplied, return "not_assessed" and focus on the candidate profile.
- Compare requirements one by one and provide evidence from the CV whenever possible.
- Prefer transparent status labels over unsupported numerical scores.
- Use these requirement statuses only: met, partial, not_found, conflicting, not_assessed.
- Use these high-level alignment statuses only: strong, moderate, limited, not_assessed.
- Distinguish evidence, interpretation, and verification needs.
- Produce useful interview questions based on gaps, ambiguous claims, or experience that should be verified.
- Never treat an omitted skill as a negative fact.
- Ignore photos and personal characteristics that are not job-relevant.
INSTRUCTIONS;
    }

    public function schema(JsonSchema $schema): array
    {
        $requirement = fn ($schema) => $schema->object(fn ($schema) => [
            'requirement' => $schema->string()->required(),
            'status' => $schema->string()
                ->enum(['met', 'partial', 'not_found', 'conflicting', 'not_assessed'])
                ->required(),
            'evidence' => $schema->string()->required(),
            'notes' => $schema->string()->required(),
        ]);

        return [
            'candidate' => $schema->object(fn ($schema) => [
                'name' => $schema->string()->required(),
                'current_or_targeted_title' => $schema->string()->required(),
                'profile_summary' => $schema->string()->required(),
                'experience_summary' => $schema->string()->required(),
                'skills' => $schema->array()->items(
                    $schema->object(fn ($schema) => [
                        'name' => $schema->string()->required(),
                        'evidence' => $schema->string()->required(),
                    ])
                )->required(),
                'education' => $schema->array()->items(
                    $schema->object(fn ($schema) => [
                        'qualification' => $schema->string()->required(),
                        'institution' => $schema->string()->required(),
                        'relevance' => $schema->string()->required(),
                    ])
                )->required(),
            ])->required(),

            'position_alignment' => $schema->object(function ($schema) use ($requirement) {
                return [
                'status' => $schema->string()
                    ->enum(['strong', 'moderate', 'limited', 'not_assessed'])
                    ->required(),
                'summary' => $schema->string()->required(),
                'requirements' => $schema->array()
                    ->items($requirement($schema))
                    ->required(),
                'jobdesk_alignment' => $schema->array()
                    ->items($schema->object(fn ($schema) => [
                        'requirement' => $schema->string()->required(),
                        'status' => $schema->string()
                            ->enum(['met', 'partial', 'not_found', 'conflicting', 'not_assessed'])
                            ->required(),
                        'evidence' => $schema->string()->required(),
                        'notes' => $schema->string()->required(),
                    ]))
                    ->required(),
                ];
            })->required(),

            'company_alignment' => $schema->object(function ($schema) use ($requirement) {
                return [
                'status' => $schema->string()
                    ->enum(['strong', 'moderate', 'limited', 'not_assessed'])
                    ->required(),
                'summary' => $schema->string()->required(),
                'criteria' => $schema->array()
                    ->items($requirement($schema))
                    ->required(),
                ];
            })->required(),

            'strengths' => $schema->array()
                ->items($schema->string())
                ->required(),

            'gaps' => $schema->array()
                ->items($schema->object(fn ($schema) => [
                    'item' => $schema->string()->required(),
                    'evidence' => $schema->string()->required(),
                    'impact' => $schema->string()->required(),
                ]))
                ->required(),

            'verification_items' => $schema->array()
                ->items($schema->string())
                ->required(),

            'interview_questions' => $schema->array()
                ->items($schema->string())
                ->required(),

            'feedback' => $schema->string()->required(),
        ];
    }
}
