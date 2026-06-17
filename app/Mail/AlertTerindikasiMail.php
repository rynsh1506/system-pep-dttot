<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AlertTerindikasiMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $nik;
    public $hasil_dtot;
    public $hasil_pep;
    public $source;
    public $kontrak;
    public $checked_by;
    public $waktu;
    public $alert_type;

    /**
     * Create a new message instance.
     */
    public function __construct($nama, $nik, $hasil_dtot, $hasil_pep, $source = 'Pengajuan Cek', $kontrak = '-', $checked_by = 'Unknown', $waktu = null)
    {
        $this->nama = $nama;
        $this->nik = $nik;
        $this->hasil_dtot = $hasil_dtot;
        $this->hasil_pep = $hasil_pep;
        $this->source = $source;
        $this->kontrak = $kontrak;
        $this->checked_by = $checked_by;
        $this->waktu = $waktu ?? now()->format('d/m/Y H:i:s');

        $terindikasi = [];
        if ($hasil_dtot === 'Terindikasi') $terindikasi[] = 'DTTOT';
        if ($hasil_pep === 'Terindikasi') $terindikasi[] = 'PEP';
        $this->alert_type = implode(' & ', $terindikasi);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ ALERT: Terindikasi ' . $this->alert_type . ' - ' . $this->nama,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-terindikasi',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
