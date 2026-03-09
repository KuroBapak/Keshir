<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashDrawerLog extends Model
{
    protected $fillable = ['cash_drawer_id', 'type', 'amount', 'description', 'transaction_id'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function cashDrawer(): BelongsTo
    {
        return $this->belongsTo(CashDrawer::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
