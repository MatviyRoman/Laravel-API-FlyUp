<?php

namespace App\Repositories;
use App\User;

/**
 * @property MailRepository mailRepository
 */
class RegistrationMailRepository
{
    public function __construct(MailRepository $mailRepository)
    {

        $this->mailRepository = $mailRepository;
    }

    public function sendConfirmationEmail(User $user, $verificationCode)
    {
        $this->mailRepository->sendSafe([
            'to'       => $user->email,
            'from'     => [config('mail.from.address') => config('mail.from.name')],
            'subject'  => 'Приглашение на регистрацию в ProApp!',
            'template' => 'emails.registration_confirmation',
            'data'     => [
                'first_name'    => $user->first_name,
                'last_name'     => $user->last_name,
                'email'         => $user->email,
                'code'          => $verificationCode,
                'link'          => env('FRONTEND_URL') . '/confirm-email/' . $verificationCode,
            ],
        ]);
    }

    public function sendRestoreConfirmationEmail(User $user, $restoreCode)
    {
        $this->mailRepository->sendSafe([
            'to'       => $user->email,
            'from'     => [config('mail.from.address') => config('mail.from.name')],
            'subject'  => 'Восстановление пароля ProApp!',
            'template' => 'emails.restore_confirmation',
            'data'     => [
                'first_name'    => $user->first_name,
                'last_name'     => $user->last_name,
                'email'         => $user->email,
                'code'          => $restoreCode,
                'link'          => env('FRONTEND_URL') . '/confirm-restore/' . $restoreCode,
            ],
        ]);
    }

}
