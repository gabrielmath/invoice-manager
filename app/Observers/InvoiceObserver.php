<?php

namespace App\Observers;

use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceObserver
{
    /**
     * Handle the Invoice "creating" event.
     */
    public function creating(Invoice $invoice): void
    {
        $invoice->number = generate_number();
        $invoice->issue_date = Carbon::now()->format('Y-m-d');
    }

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        // TODO: send notification to user who owns the invoice
    }
}
