# DNS Monitor

## Change /app/config.php

```php
'php_path' => '/usr/local/bin/php',
```
should point to the php path.

```
which php
```

## The mail logic is in the /app/filters.php file

I set up your email to recieve the notifications.

for the email to work just fill the **/app/config/mail.php** with your email credentials.

This Event listener is triggered from 
/app/controllers/MasterServerController.php

method: addNotification

```php
Event::fire('notification.new.email', array($client->id));
```

## The Monitor artisan command

is written on **/app/commands/MonitorCommand.php**

this command does a request to the *Monitor* route (or *Monitor-mock*) in the fire() method.