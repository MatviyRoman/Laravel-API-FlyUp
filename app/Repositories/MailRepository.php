<?php

namespace App\Repositories;

use App\Mail\MailLetter;
use App\Mail\QueueMailLetter;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;

/**
 * Class MailRepository
 * @package App\Repositories
 */
class MailRepository
{
    /**
     * @param array $data [
     *    'template'    => 'emails.template_name',
     *    'to'          => 'email@mail.com' | ['email@mail.com' => 'Person Name', 'email@mail.com', ...],
     *    'subject'     => 'Subject',
     *    'delay_time'  => '+10 seconds',
     *    'reply'       => 'email@mail.com' | ['email@mail.com' => 'Person Name', 'email@mail.com', ...],
     *    'bcc'         => 'email@mail.com' | ['email@mail.com' => 'Person Name', 'email@mail.com', ...],
     *    'cc'          => 'email@mail.com' | ['email@mail.com' => 'Person Name', 'email@mail.com', ...],
     *    'data'        => ['key' => 'value', ...],
     *    'attachments' => [['filename' => '/dir/users.csv'(, 'as' => 'Users.csv' optional)], ...],
     * ];
     *
     * @param bool $forceSend
     */
    public function send(array $data, $forceSend = false)
    {
        $bcc = isset($data['bcc']) ? $data['bcc'] : [];

        $bcc_limit = env("MAIL_BCC_LIMIT", 25);

        if (count($bcc) <= $bcc_limit){
            $forceSend ? self::sendMailImmediately($data) : self::sendMailViaQueue($data);
        }
        else {
            collect($bcc)->chunk($bcc_limit)->each(function ($bcc_limited) use ($data, $forceSend) {
                /** @var Collection $bcc_limited */
                if ($forceSend) {
                    self::sendMailImmediately(
                        array_merge($data, [
                            'bcc' => $bcc_limited->all()
                        ])
                    );
                } else {
                    self::sendMailViaQueue(
                        array_merge($data, [
                            'bcc' => $bcc_limited->all()
                        ])
                    );
                }
            });
        }
    }

    /**
     * @param array $data [
     *    'template'    => 'emails.template_name',
     *    'to'          => 'email@mail.com' | ['email@mail.com' => 'Person Name', 'email@mail.com', ...],
     *    'subject'     => 'Subject',
     *    'reply'       => 'email@mail.com' | ['email@mail.com' => 'Person Name', 'email@mail.com', ...],
     *    'bcc'         => 'email@mail.com' | ['email@mail.com' => 'Person Name', 'email@mail.com', ...],
     *    'cc'          => 'email@mail.com' | ['email@mail.com' => 'Person Name', 'email@mail.com', ...],
     *    'data'        => ['key' => 'value', ...],
     *    'attachments' => [['filename' => '/dir/users.csv'(, 'as' => 'Users.csv' optional)], ...],
     * ];
     * @param bool $forceSend
     */
    public function sendSafe(array $data, $forceSend = false)
    {
        try {
            $this->send($data, $forceSend);
        } catch (\Throwable $e) {
            \Log::error('Mail sent failed: ' . $e->getMessage(), $data);
        }
    }

    /**
     * @param array $data
     * @throws HttpResponseException
     */
    protected static function sendMailViaQueue(array $data)
    {
        $mailData = new QueueMailLetter($data);

        //delay setting
        if (!empty($data['delay_time'])) {

            $delayTime = new Carbon($data['delay_time']);

            if ($delayTime->diffInSeconds(Carbon::now()) <= 0) {
                throw new HttpResponseException(response('The "Delayed" property for mail is not provided. Please correct it.', 422));
            } else {

                $mailData->delay($delayTime->diffInSeconds(Carbon::now()));

                \Mail::later($delayTime->diffInSeconds(Carbon::now()), $mailData);

                return;
            }
        }

        \Mail::queue($mailData);
    }

    /**
     * @param array $data
     */
    protected static function sendMailImmediately(array $data)
    {
        \Mail::send(new MailLetter($data));
    }

    /**
     * @param string $dateTime
     * @return string
     */
    public function createDateTimeStringForMail($dateTime)
    {
        $carbonObj = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime);

        return $carbonObj->format('m/d/Y \\a\\t h:iA');
    }
}
