<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PedidoMail extends Mailable
{
    use SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->data['subject'] ?? 'Nuevo Pedido - ' . ($this->data['cliente']['nombre'] ?? 'Cliente'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pedido',
        );
    }

    public function attachments(): array
    {
        if (!empty($this->data['archivo_path']) && is_file($this->data['archivo_path'])) {
            $attachment = Attachment::fromPath($this->data['archivo_path'])
                ->as($this->data['archivo_nombre'] ?? basename($this->data['archivo_path']));

            if (!empty($this->data['archivo_mime'])) {
                $attachment = $attachment->withMime($this->data['archivo_mime']);
            }

            return [$attachment];
        }

        return [];
    }
}
