<?php

Event::listen('notification.new.email', function($client_id){

    Mail::send('emails.notify', array('notification' => Notification::with('notification_server')->orderBy('id', 'DESC')->first()), function($message) use ($client_id)
    {
        $message->to('jpescalonag@gmail.com', 'John Smith')->subject('New IP found for client '. $client_id);
    });
});