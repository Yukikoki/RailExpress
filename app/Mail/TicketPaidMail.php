<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketPaidMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking; // Deklarasikan variabel ini agar bisa dibaca di view email

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('E-Tiket RailExpress Anda - ' . $this->booking->booking_code)
                    ->view('emails.ticket_paid'); // Pastikan file view ini ada
    }
}
