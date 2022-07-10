# Tinkermail PHP SDK

[Tinkermail](https://www.tinkermail.io) is a simple interface that lets you integrate quickly to Amazon SES, Gmail or any other SMTP server to send your transactional emails easily and pretty much for FREE.

## Installation

```sh
$ composer require tinkermail/tinkermail-php
```

## Usage

```php
$client = new Tinkermail\TinkermailPhp\TinkermailClient("YOUR_API_KEY");

$contact = new Tinkermail\TinkermailPhp\Contact(
    "johndoe@example.com", 
    [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'onboarded' => false
    ]
);
// Push your contacts to tinkermail with as many attributes as you want
$client->updateContact($contact);

// Send them emails using predefined templates
$client->sendMail(
    "johndoe@example.com",
    "customer-welcome-email",
    [
        'username' => 'johndoe'
    ]
);

// You can also do the same asynchronously
$client->updateContactAsync($contact);

$client->sendMailAsync(
    "johndoe@example.com",
    "customer-welcome-email",
    [
        'username' => 'johndoe'
    ]
);
```

## We accept contributions here

If you're a PHP developer using Tinkermail and want to contribute to this SDK, we're more than happy to have your pull request here - and your name on the hall of fame forever!
