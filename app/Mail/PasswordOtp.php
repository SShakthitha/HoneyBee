<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordOtp extends Mailable
{
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $otp,
        public int $expiryMinutes,
    ) {
    }

    /** Build the message. */
    public function build(): self
    {
        return $this->subject('Your HoneyBee password reset code')
            ->markdown('mail.password-otp', [
                'user' => $this->user,
                'otp' => $this->otp,
                'expiryMinutes' => $this->expiryMinutes,
            ]);
    }
}
