
<p align="center"><img src="/art/bento-php-header.png" alt="Bento PHP SDK"></p>

[![Build Status](https://travis-ci.org/bentonow/bento-php-sdk.svg?branch=master)](https://travis-ci.org/bentonow/bento-php-sdk)

> [!TIP]
> Need help? Join our [Discord](https://discord.gg/ssXXFRmt5F) or email jesse@bentonow.com for personalized support.

The Bento PHP SDK makes it quick and easy to build an excellent analytics experience in your PHP application. We provide powerful and customizable APIs that can be used out-of-the-box to track your users' behavior, manage subscribers, and send emails. We also expose low-level APIs so that you can build fully custom experiences.

Get started with our [📚 integration guides](https://docs.bentonow.com), or [📘 browse the SDK reference](https://docs.bentonow.com/subscribers).

❤️ Thank you @cavel (in Discord) from [GuitarCreative](https://guitarcreative.com) for your contribution to the Laravel documentation.


Table of contents
=================

<!--ts-->
* [Features](#features)
* [Requirements](#requirements)
* [Getting started](#getting-started)
    * [Installation](#installation)
    * [Configuration](#configuration)
* [Modules](#modules)
* [Type Reference](#types-reference)
* [Things to Know](#things-to-know)
* [Contributing](#contributing)
* [License](#license)
<!--te-->

## Features

* **Simple event tracking**: We make it easy for you to track user events and behavior in your application.
* **Subscriber management**: Easily add, update, and remove subscribers from your Bento account.
* **Custom fields**: Track and update custom fields for your subscribers to store additional data.
* **Purchase tracking**: Monitor customer purchases and calculate lifetime value (LTV) for your subscribers.
* **Batch operations**: Perform bulk imports of subscribers and events for efficient data management.
* **Email validation**: Validate email addresses to ensure data quality.

## Requirements

The Bento PHP SDK requires PHP 7.4+ and Composer.

Bento Account for a valid **SITE_UUID**, **BENTO_PUBLISHABLE_KEY** & **BENTO_SECRET_KEY**.

## Getting started

### Installation

Install the Bento SDK using Composer:

```bash
composer require bentonow/bento-php-sdk
```

### Configuration

Initialize the Bento client:

```php
use bentonow\Bento\BentoAnalytics;

$bento = new BentoAnalytics([
  'authentication' => [
    'secretKey' => 'bento-secret-key',
    'publishableKey' => 'bento-publishable-key'
  ],
  'siteUuid' => 'bento-site-uuid'
]);
```

## Modules

### Analytics (Base Module)

Track events and manage subscribers.

#### Tag a Subscriber

```php
$bento->V1->tagSubscriber([
  'email' => 'user@example.com',
  'tagName' => 'New Customer',
]);
```

#### Add a Subscriber

```php
$bento->V1->addSubscriber([
  'email' => 'user@example.com',
  'fields' => [
    'firstName' => 'John',
    'lastName' => 'Doe',
  ],
]);
```

#### Remove a Subscriber

```php
$bento->V1->removeSubscriber([
  'email' => 'user@example.com',
]);
```

#### Update Fields

```php
$bento->V1->updateFields([
  'email' => 'user@example.com',
  'fields' => [
    'firstName' => 'John',
  ],
]);
```

#### Track Purchase

```php
$bento->V1->trackPurchase([
  'email' => 'user@example.com',
  'purchaseDetails' => [
    'unique' => [
      'key' => 1234,
    ],
    'value' => [
      'amount' => 100,
      'currency' => 'USD',
    ],
  ],
]);
```

#### Track Event

```php
$bento->V1->track([
  'email' => 'user@example.com',
  'type' => '$custom.event',
  'details' => [
    'fromCustomEvent' => true,
  ],
]);
```

### Batch

#### Import Subscribers

```php
$bento->V1->Batch->importSubscribers([
  'subscribers' => [
    ['email' => 'user1@example.com', 'age' => 25],
    ['email' => 'user2@example.com', 'name' => 'Jane Doe'],
  ]
]);
```

#### Import Events

```php
use bentonow\Bento\SDK\Batch\BentoEvents;

$bento->V1->Batch->importEvents([
  'events' => [
    ['email' => 'user@example.com', 'type' => BentoEvents::SUBSCRIBE],
    ['email' => 'user@example.com', 'type' => BentoEvents::UNSUBSCRIBE],
    [
      'email' => 'user@example.com',
      'details' => [
        'customData' => 'Used internally.'
      ],
      'type' => '$custom.myEvent'
    ]
  ]
]);
```

### Commands

#### Add Tag

```php
$bento->V1->Commands->addTag([
  'email' => 'user@example.com',
  'tagName' => 'VIP',
]);
```

#### Remove Tag

```php
$bento->V1->Commands->removeTag([
  'email' => 'user@example.com',
  'tagName' => 'VIP',
]);
```

#### Add Field

```php
$bento->V1->Commands->addField([
  'email' => 'user@example.com',
  'field' => [
    'key' => 'favoriteColor',
    'value' => 'blue',
  ],
]);
```

#### Remove Field

```php
$bento->V1->Commands->removeField([
  'email' => 'user@example.com',
  'fieldName' => 'favoriteColor',
]);
```

#### Subscribe

```php
$bento->V1->Commands->subscribe([
  'email' => 'user@example.com',
]);
```

#### Unsubscribe

```php
$bento->V1->Commands->unsubscribe([
  'email' => 'user@example.com',
]);
```

### Events

#### Create Event

```php
$bento->V1->Events->createEvent([
  'type' => '$completed_onboarding',
  'email' => 'user@example.com',
]);
```

### Experimental

#### Validate Email

```php
$bento->V1->Experimental->validateEmail([
  'email' => 'user@example.com',
]);
```

#### Guess Gender

```php
$bento->V1->Experimental->guessGender([
  'name' => 'Alex',
]);
```

#### Geolocate

```php
$bento->V1->Experimental->geolocate([
  'ip' => '127.0.0.1',
]);
```

#### Check Blacklist

```php
$bento->V1->Experimental->checkBlacklist([
  'domain' => 'example.com',
]);
```

### Fields

#### Get Fields

```php
$fields = $bento->V1->Fields->getFields();
```

#### Create Field

```php
$bento->V1->Fields->createField([
  'key' => 'favoriteColor',
]);
```

### Forms

#### Get Form Responses

```php
$responses = $bento->V1->Forms->getResponses('form-id-123');
```

### Subscribers

#### Get Subscriber

```php
$subscriber = $bento->V1->Subscribers->getSubscribers([
  'email' => 'user@example.com',
]);
```

#### Create Subscriber

```php
$bento->V1->Subscribers->createSubscriber([
  'email' => 'newuser@example.com',
]);
```

### Tags

#### Get Tags

```php
$tags = $bento->V1->Tags->getTags();
```

#### Create Tag

```php
$bento->V1->Tags->createTag([
  'name' => 'Premium',
]);
```

## Types Reference

For a detailed reference of the types used in the Bento PHP SDK, please refer to the [Types Reference](https://docs.bentonow.com) section in the full documentation.

## Things to Know

1. All events must be identified with an email address.
2. Most events are indexed within seconds in your Bento account.
3. Batch operations are available for importing subscribers and events efficiently.
4. The SDK provides seamless integration with Laravel applications.
5. Email validation and experimental features are available for advanced use cases.

## Contributing

We welcome contributions! Please see our [contributing guidelines](CODE_OF_CONDUCT.md) for details on how to submit pull requests, report issues, and suggest improvements.

## License

The Bento SDK for PHP is available as open source under the terms of the [MIT License](LICENSE.md).