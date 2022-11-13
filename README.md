# SendStreak PHP SDK

[SendStreak](https://www.sendstreak.com) is a simple interface that lets you integrate quickly with email services such as Mailchimp, Sendgrid or even AWS SES or Gmail to decouple your audience, email history and templates from your email provider.

## Installation

```sh
$ composer require sendstreak/sendstreak-php
```

## Usage

```php
$client = new SendStreak\SendStreakPhp\SendStreakClient("YOUR_API_KEY");

$contact = new SendStreak\SendStreakPhp\Contact(
    "johndoe@example.com", 
    [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'onboarded' => false
    ]
);
// Push your contacts to SendStreak with as many attributes as you want
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

If you're a PHP developer using SendStreak and want to contribute to this SDK, we're more than happy to have your pull request here - and your name on the hall of fame forever!

## Hall of fame

13.07.2023 [Daniel Martin Bettenbuk](https://github.com/FractalXX) - Initial version of SendStreak PHP SDK
