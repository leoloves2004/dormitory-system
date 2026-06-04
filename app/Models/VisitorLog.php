<?php

namespace App\Models;

use Database\Factories\VisitorLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tenant_id', 'visitor_name', 'visitor_phone', 'visit_date', 'purpose', 'time_in', 'time_out'])]
class VisitorLog extends Model
{
    /** @use HasFactory<VisitorLogFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['visit_date' => 'date', 'time_in' => 'datetime', 'time_out' => 'datetime'];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
