<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuotationSent extends Mailable
{
    use Queueable, SerializesModels;

    public Quotation $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function build()
    {
        $q = $this->quotation->load('project.client');
        $mail = $this->subject('Quotation #' . $q->id . ' - ' . $q->project->name)
            ->view('emails.quotation_sent', ['q' => $q]);

        if ($q->pdf_path && \Storage::disk('public')->exists($q->pdf_path)) {
            $mail->attachFromStorageDisk('public', $q->pdf_path);
        }

        return $mail;
    }
}
