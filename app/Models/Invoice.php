<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    private string $documentMask = '##.###.###/####-##';
    protected $fillable = [
        'user_id',
        'value',
        'sender_document',
        'sender_name',
        'transporter_document',
        'transporter_name',
    ];

    protected $casts = [
        'issue_date' => 'date'
    ];

    /**
     * Insert mask in sender document
     *
     * @return Attribute
     */
    protected function moneyValue(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => 'R$ ' . number_format($attributes['value'], 2, ',', '.'),
        );
    }

    /**
     * Insert mask in sender document
     *
     * @return Attribute
     */
    protected function senderDoc(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => mask($attributes['sender_document'], $this->documentMask),
        );
    }

    public function setSenderDocumentAttribute($value): void
    {
        $this->attributes['sender_document'] = preg_replace('/[^0-9]/is', '', $value);
    }

    /**
     * Insert mask in transporter document
     *
     * @return Attribute
     */
    protected function transporterDoc(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => mask($attributes['transporter_document'], $this->documentMask),
        );
    }

    public function setTransporterDocumentAttribute($value): void
    {
        $this->attributes['transporter_document'] = preg_replace('/[^0-9]/is', '', $value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
