<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Notifications\InvoiceCreatedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Invoice $invoice)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->invoice->user->notify(new InvoiceCreatedNotification($this->invoice));
    }
}
