<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use JPush\Client;

class PushNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DatabaseNotification $databaseNotification)
    {
        if(app()->environment('local')) {
            return;
        }

        $user = $databaseNotification->notifiable;

        if(!$user->registration_id) {
            return;
        }

        $this->client->push()
            ->setPlatform('all')
            ->addRegistrationId($user->registratoin_id)
            ->setNotificationAlert(strip_tags($databaseNotification->data['reply_content']))
            ->send();
    }
}
