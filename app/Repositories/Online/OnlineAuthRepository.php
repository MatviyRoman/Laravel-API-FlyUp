<?php

namespace App\Repositories\Online;

use Illuminate\Support\Facades\Password;

class OnlineAuthRepository
{

    /**
     * Send reset password for broker (by default online or changed value for default guard)
     *
     * @param $email
     * @param null $broker
     * @return array|null
     */
    public function sendResetPassword($email, $broker = null)
    {
        $credentials = ['email' => $email];

        //this should be called to don't mix user triggered passwords with admin or programmatically calls
        // current method used only in admin side so...
        \Session::forget('url');

        $response = Password::broker($broker)->sendResetLink($credentials);

        if (Password::RESET_LINK_SENT) {
            return ['status' => 'OK'];
        }

        if (Password::INVALID_USER) {
            //throw new RecValidationException(['email' => trans($response)]);
            die('123');
        }
    }
}