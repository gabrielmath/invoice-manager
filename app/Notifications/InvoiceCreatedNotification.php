<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Invoice $invoice)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nota Fiscal  cadastrada')
            ->greeting("Olá, {$notifiable->name}!")
            ->line('Seguem os dados abaixo:')
            ->line("ID: {$this->invoice->id}")
            ->line("Número: {$this->invoice->number}")
            ->line("Data de Emissão: {$this->invoice->issue_date->format('d/m/Y')}")
            ->line("Valor: {$this->invoice->money_value}")
            ->line("CNPJ do Remetente: {$this->invoice->sender_doc}")
            ->line("Nome do Remetente: {$this->invoice->sender_name}")
            ->line("CNPJ do Transportador: {$this->invoice->transporter_doc}")
            ->line("Nome do Transportador: {$this->invoice->transporter_name}")
            ->line('Obrigado por confiar em nosso trabalho!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
