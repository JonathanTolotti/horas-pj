<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BackupMail extends Mailable
{
    public function __construct(
        private string $zipPath,
        private string $fileName,
        private float  $sizeKb,
        private string $connection,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Horas PJ] Backup do banco de dados — ' . now()->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: view('emails.backup', [
                'fileName'   => $this->fileName,
                'sizeKb'     => $this->sizeKb,
                'connection' => $this->connection,
                'executedAt' => now()->format('d/m/Y \à\s H:i'),
            ])->render(),
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->zipPath)->as($this->fileName)->withMime('application/zip'),
        ];
    }
}
