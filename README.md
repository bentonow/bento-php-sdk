# Bento SDK for PHP [![Build Status](https://github.com/bentonow/bento-php-sdk/workflows/Tests/badge.svg)](https://github.com/bentonow/bento-php-sdk)

🍱 Simple, powerful analytics for PHP projects!

Track events, update data, record LTV and more in PHP. Data is stored in your Bento account so you can easily research and investigate what's going on.

👋 To get personalized support, please tweet @bento or email jesse@bentonow.com!

🐶 Find a bug? Join us on Discord and let us know!

🔥 Thank you, @arvesolland for the initial commit to this library.

## Installation

This library can be installed via [Composer](https://getcomposer.org):

```bash
composer require bentonow/bento-php-sdk 
```

## Usage

Before tracking user or event data, create a new client. If you configured your site uuid via environment variables (BENTO_SITE_UUID) there's nothing to add. Otherwise, see the example above.

```php
// Via .env variables
$bento = new new bentonow\Bento\Bento();
```

or directly inject the site uuid
```php
$bento = new new bentonow\Bento\Bento('YOURSITEUUIDHERE');
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
$bento = new bentonow\Bento\Bento();
$bento->identify('user@example.org');
$bento->updateFields(['first_name'=>'ash','last_name'=>'ketchum]);
$bento->track('$signUp',['plan'=>'Free Trial']);
```

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/bentonow/bento-ruby-sdk. This project is intended to be a safe, welcoming space for collaboration, and contributors are expected to adhere to the [Contributor Covenant](http://contributor-covenant.org) code of conduct.


## License

The gem is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).
