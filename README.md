# Bento SDK for PHP

🍱 Simple, powerful analytics for PHP projects!

Track events, update data, record LTV and more in PHP. Data is stored in your Bento account so you can easily research and investigate what's going on.

👋 To get personalized support, please tweet @bento or email jesse@bentonow.com!

🐶 Battle-tested on Bento Production!

## Installation

Add this line to your application's Gemfile:

## Installation

This library can be installed via [Composer](https://getcomposer.org):

```bash
composer require bentonow/bento-php-sdk dev-master
```



## Usage

Before tracking user or event data, create a new client. If you configured your site uuid via environment variables (BENTO_SITE_UUID) there's nothing to add. Otherwise, see the example above.

```php
// Via .env variables
$bento = new \Bento();
```

or directly inject the site uuid
```php
$bento = new \Bento('YOURSITEUUIDHERE');
```

   

### Tracking Users

#### Identify your user

```php
// if you have their email address, identify the user.
// do this before anything else.
$bento->identify('user@example.org');

```

#### Tag a visitor

```php
$bento->tag('tag_1,tag_2');

```

#### Log a custom event

```php
$bento->track('some_event',['some_key'=>'Some Value']);

```

#### Update custom fields

```php
// you can add custom fields to your visitors which you can leverage for personalization.
$bento->updateFields(['first_name'=>'ash','last_name'=>'ketchum]);

```

#### Full Example
```php
$bento = new \Bento();
$bento->identify('user@example.org');
$bento->updateFields(['first_name'=>'ash','last_name'=>'ketchum]);

```

