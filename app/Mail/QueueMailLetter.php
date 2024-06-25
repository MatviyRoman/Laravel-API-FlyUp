<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class QueueMailLetter
 * @package App\Mail
 */
class QueueMailLetter extends MailLetter implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue {
        fail as baseFail;
    }

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;


    public function fail(?\Exception $exception = null)
    {
        Log::error($exception);

        $this->baseFail($exception);
    }
}
