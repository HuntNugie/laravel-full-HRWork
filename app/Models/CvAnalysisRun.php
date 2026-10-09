<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvAnalysisRun extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'status',
        'original_filename',
        'file_path',
        'file_disk',
        'position_id',
        'company_criteria',
        'result',
        'error_message',
        'started_at',
        'finished_at',
    ];

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'result' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
