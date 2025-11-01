<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'txn_date', 'type', 'amount', 'currency', 'exchange_rate',
        'bank_name', 'account_number', 'account_label',
        'project_id', 'counterparty', 'reference', 'description', 'reconciled',
        'attachment_path', 'created_by'
    ];

    protected $casts = [
        'txn_date' => 'date',
        'reconciled' => 'boolean',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signedAmount(): float
    {
        $negTypes = ['withdrawal', 'transfer_out', 'fee'];
        return in_array($this->type, $negTypes, true) ? -1 * (float)$this->amount : (float)$this->amount;
    }
}
