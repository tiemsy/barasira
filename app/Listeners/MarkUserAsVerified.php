<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Verified;

class MarkUserAsVerified
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        logger()->info('EVENT VERIFIED FIRED', [
            'user_id' => $event->user->id
        ]);

        $user = $event->user;

        if (!$user->verified) {
            $user->update(['verified' => true]);
        }
    }
}
