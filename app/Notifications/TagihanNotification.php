<?php

namespace App\Notifications;

use App\Services\FontteService;
use Illuminate\Notifications\Notification;

class TagihanNotification extends Notification
{
    protected $tagihan;

    public function __construct($tagihan)
    {
        $this->tagihan = $tagihan;
    }

    public function via($notifiable)
    {
        return ['fonnte'];
    }

    public function toFonnte($notifiable)
    {
        try {
            $message = "Yth. {$notifiable->name}\n\n"
                . "Anda memiliki tagihan baru:\n"
                . "Nominal: Rp " . number_format($this->tagihan->total_tagihan, 0, ',', '.') . "\n"
                . "Jatuh Tempo: " . date('d-m-Y', strtotime($this->tagihan->tanggal_jatuh_tempo)) . "\n\n"
                . "Mohon segera lakukan pembayaran. Terima kasih.";

            $fontte = new FontteService();
            return $fontte->sendSMS($notifiable->no_telepon, $message); // Ganti phone jadi no_telepon
        } catch (\Exception $e) {
            \Log::error('Error di TagihanNotification: ' . $e->getMessage());
            throw $e;
        }
    }
}