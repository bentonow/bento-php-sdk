# Bento SDK for PHP

🍱 Simple, powerful analytics for PHP (and Laravel) projects!

Track events, update data, record LTV and more in PHP. Data is stored in your Bento account so you can easily research and investigate what's going on.

👋 To get personalized support, please tweet @bento or email jesse@bentonow.com!

❤️ Thank you @cavel (in Discord) from [GuitarCreative](https://guitarcreative.com) for your contribution to the Laravel documentation.

-   [Installation](#Installation)
-   [Installation for Laravel](#Installation-Laravel)
-   [Get Started](#Get-Started)
-   [Modules](#Modules)
    -   [Analytics (Base Module)](#analytics-base-module)
        -   [tagSubscriber(parameters: TagSubscriberParameters): boolean](#tagsubscriberparameters-tagsubscriberparameters-boolean)
        -   [addSubscriber(parameters: AddSubscriberParameters): boolean](#addsubscriberparameters-addsubscriberparameters-boolean)
        -   [removeSubscriber(parameters: RemoveSubscriberParameters): boolean](#removesubscriberparameters-removesubscriberparameters-boolean)
        -   [updateFields(parameters: UpdateFieldsParameters): boolean](#updatefieldsparameters-updatefieldsparameters-boolean)
        -   [trackPurchase(parameters: TrackPurchaseParameters): boolean](#trackpurchaseparameters-trackpurchaseparameters-boolean)
    -   [Batch](#Batch)
        -   [.importSubscribers(parameters: BatchImportSubscribersParameter): number](#batchimportsubscribersparameters-batchimportsubscribersparameter-number)
        -   [.importEvents(parameters: BatchImportEventsParameter): number](#batchimporteventsparameters-batchimporteventsparameter-number)
    -   [Commands](#Commands)
        -   [.addTag(parameters: AddTagParameters): Subscriber | null](#commandsaddtagparameters-addtagparameters-subscriber--null)
        -   [.removeTag(parameters: RemoveTagParameters): Subscriber | null](#commandsremovetagparameters-removetagparameters-subscriber--null)
        -   [.addField(parameters: AddFieldParameters): Subscriber | null](#commandsaddfieldparameters-addfieldparameters-subscriber--null)
        -   [.removeField(parameters: RemoveFieldParameters): Subscriber | null](#commandsremovefieldparameters-removefieldparameters-subscriber--null)
        -   [.subscribe(parameters: SubscribeParameters): Subscriber | null](#commandssubscribeparameters-subscribeparameters-subscriber--null)
        -   [.unsubscribe(parameters: UnsubscribeParameters): Subscriber | null](#commandsunsubscribeparameters-unsubscribeparameters-subscriber--null)
    -   [Experimental](#Experimental)
        -   [.validateEmail(parameters: ValidateEmailParameters): boolean](#experimentalvalidateemailparameters-validateemailparameters-boolean)
        -   [.guessGender(parameters: GuessGenderParameters): GuessGenderResponse](#experimentalguessgenderparameters-guessgenderparameters-guessgenderresponse)
        -   [.geolocate(parameters: GeolocateParameters): LocationData | null](#experimentalgeolocateparameters-geolocateparameters-locationdata--null)
        -   [.checkBlacklist(parameters: BlacklistParameters): BlacklistResponse](#experimentalcheckblacklistparameters-blacklistparameters-blacklistresponse)
    -   [Fields](#Fields)
        -   [.getFields(): Field[] | null](#fieldsgetfields-field--null)
        -   [.createField(parameters: CreateFieldParameters): Field[] | null](#fieldscreatefieldparameters-createfieldparameters-field--null)
    -   [Forms](#Forms)
        -   [.getResponses(formIdentifier: string): FormResponse[] | null](#formsgetresponsesformidentifier-string-formresponse--null)
    -   [Subscribers](#Subscribers)
        -   [.getSubscribers(parameters?: GetSubscribersParameters): Subscriber | null](#subscribersgetsubscribersparameters-getsubscribersparameters-subscriber--null)
        -   [.createSubscriber(parameters: CreateSubscriberParameters): Subscriber | null](#subscriberscreatesubscriberparameters-createsubscriberparameters-subscriber--null)
    -   [Tags](#Tags)
        -   [.getTags(): Tag[] | null](#tagsgettags-tag--null)
        -   [.createTag(parameters: CreateTagParameters): Tag[] | null](#tagscreatetagparameters-createtagparameters-tag--null)
-   [Types Reference](#Types-Reference)
-   [Things to Know](#Things-to-Know)
-   [Contributing](#Contributing)
-   [License](#License)

## Installation

Run the following command in your project folder. (Note, this project requires [Composer](https://getcomposer.org/))

```bash
composer require bentonow/bento-php-sdk
```

## Installation Laravel

If you want to make the Bento instance accessible throughout your application, you might want to consider using a Service Provider. Service Providers in Laravel are central to bootstrapping all of the framework's various components, like routing, events, etc. Here's a basic guide:

### Step 1: Create a new service provider:

You can use the artisan command to generate a new service provider:
```php
php artisan make:provider BentoServiceProvider
```
This will create a new file in `app/Providers`.

### Step 2: Register the service in the new provider

Open the newly created provider file. In the register method, bind the Bento instance to the service container. The register method is the perfect place to bind items to the service container:
```php
// Add this at the top of the file
use bentonow\Bento\BentoAnalytics;

public function register()
{
    $this->app->singleton(BentoAnalytics::class, function ($app) {
        return new BentoAnalytics([
            'authentication' => [
                'secretKey' => env('BENTO_SECRET_KEY'),
                'publishableKey' => env('BENTO_PUBLISHABLE_KEY')
            ],
            'siteUuid' => env('BENTO_SITE_UUID')
        ]);
    });
}
```
Note that the `env()` function is used to get the values from your environment variables. Replace `'BENTO_SECRET_KEY'`, `'BENTO_PUBLISHABLE_KEY'`, and `'BENTO_SITE_UUID'` with your actual environment variable names.

### Step 3: Register the Service Provider

In `config/app.php`, add your new service provider to the providers array:
```php
'providers' => [
    // Other Service Providers

    App\Providers\BentoServiceProvider::class,
],
```

Now, you can resolve (or "get") the Bento instance out of the service container anywhere in your application using dependency injection or the app() helper function:
```php
$bento = app(BentoAnalytics::class);
```

Or you can use dependency injection in your controller method:
```php
public function someMethod(BentoAnalytics $bento) 
{
    // Use $bento here...
}
```
This way, you're adhering to the Dependency Inversion Principle, one of the SOLID principles of object-oriented design, which can lead to more maintainable and flexible code.

## Get Started

To get started with tracking things in Bento, simply initialize the client and run wild!

```php
use bentonow\Bento\BentoAnalytics;

// 1. Create the Bento client.
$bento = new BentoAnalytics([
  'authentication' => [
    'secretKey' => 'secretKey',
    'publishableKey' => 'publishableKey'
  ],
  'siteUuid' => 'siteUuid'
])

# Send in a custom event that can trigger an automation — this will also create the user in your account, no need for a second call!
# We strongly recommend using track() for most real-time things.
$bento->V1->track([
  'email' => 'test@bentonow.com',
  'type' => '$signed_up',
  'details' => [
    'fromCustomEvent' => true
  ]
]);

// Track a custom unique event (purchase, sale, etc).
$bento->V1->trackPurchase([
  'email' => 'test@bentonow.com',
  'purchaseDetails' => [
    'unique' => [
      'key' => 1234,
    ],
    'value' => [
      'amount' => 100,
      'currency' => 'USD',
    ],
    'cart' => [
      'abandoned_checkout_url' => ''
    ]
  ]
])
```

Read on to see what all you can do with the SDK.

# Modules

In addition to the top-level Analytics object, we also provide access to other parts of the API behind their corresponding modules. You can access these off of the main `Analytics` object.

The `Analytics` module also provides access to various versions of the API (currently just `V1`), and each of those provides access to the corresponding modules documented below.

## Analytics (Base Module)

### `tagSubscriber(parameters: TagSubscriberParameters): boolean`

**This TRIGGERS automations!** - If you do not wish to trigger automations, please use the [`Commands.addTag`](#commandsaddtagparameters-addtagparameters-subscriber--null) method.

Tags a subscriber with the specified email and tag. If either the tag or the user do not exist, they will be created in the system. If the user already has the tag, another tag event will be sent, triggering any automations that take place upon a tag being added to a subscriber. Please be aware of the potential consequences.

Because this method uses the batch API, the tag may take between 1 and 3 minutes to appear in the system.

Returns `true` if the event was successfully dispatched. Returns `false` otherwise.

Reference Types: [TagSubscriberParameters](#TagSubscriberParameters)

```php
$bento->V1->tagSubscriber([
  'email' => 'test@bentonow.com',
  'tagName' => 'Test Tag',
]);
```

---

### `addSubscriber(parameters: AddSubscriberParameters): boolean`

**This TRIGGERS automations!** - If you do not wish to trigger automations, please use the [`Commands.subscribe`](#commandssubscribeparameters-subscribeparameters-subscriber--null) method.

Creates a subscriber in the system. If the subscriber already exists, another subscribe event will be sent, triggering any automations that take place upon subscription. Please be aware of the potential consequences.

You may optionally pass any fields that you wish to be set on the subscriber during creation.

Because this method uses the batch API, the tag may take between 1 and 3 minutes to appear in the system.

Returns `true` if the event was successfully dispatched. Returns `false` otherwise.

Reference Types: [AddSubscriberParameters](#addsubscriberparameters)

```php
$bento->V1->addSubscriber([
  'email' => 'test@bentonow.com'
]);

$bento->V1->addSubscriber([
  'date' => '2021-08-20T01:32:57.530Z',
  'email' => 'test@bentonow.com',
  'fields' => [
    'firstName' => 'Test',
    'lastName' => 'Subscriber'
  ]
]);
```

---

### `removeSubscriber(parameters: RemoveSubscriberParameters): boolean`

**This TRIGGERS automations!** - If you do not wish to trigger automations, please use the [`Commands.unsubscribe`](#commandsunsubscribeparameters-unsubscribeparameters-subscriber--null) method.

Unsubscribes an email in the system. If the email is already unsubscribed, another unsubscribe event will be sent, triggering any automations that take place upon an unsubscribe happening. Please be aware of the potential consequences.

Because this method uses the batch API, the tag may take between 1 and 3 minutes to appear in the system.

Returns `true` if the event was successfully dispatched. Returns `false` otherwise.

Reference Types: [RemoveSubscriberParameters](#RemoveSubscriberParameters)

```php
$bento->V1->removeSubscriber([
  'email' => 'test@bentonow.com'
]);
```

---

### `updateFields(parameters: UpdateFieldsParameters): boolean`

**This TRIGGERS automations!** - If you do not wish to trigger automations, please use the [`Commands.addField`](#commandsaddfieldparameters-addfieldparameters-subscriber--null) method.

Sets the passed-in custom fields on the subscriber, creating the subscriber if it does not exist. If the fields are already set on the subscriber, the event will be sent, triggering any automations that take place upon fields being updated. Please be aware of the potential consequences.

Because this method uses the batch API, the tag may take between 1 and 3 minutes to appear in the system.

Returns `true` if the event was successfully dispatched. Returns `false` otherwise.

Reference Types: [UpdateFieldsParameters](#updatefieldsparameters)

```php
$bento->V1->updateFields([
  'email' => 'test@bentonow.com',
  'fields' => [
    'firstName' => 'Test',
  ]
]);
```

---

### `trackPurchase(parameters: TrackPurchaseParameters): boolean`

**This TRIGGERS automations!** - There is no way to achieve this same behavior without triggering automations.

Tracks a purchase in Bento, used to calculate LTV for your subscribers. The values that are received should be numbers, in cents. For example, `$1.00` should be `100`.

Because this method uses the batch API, the tag may take between 1 and 3 minutes to appear in the system.

Returns `true` if the event was successfully dispatched. Returns `false` otherwise.

Reference Types: [TrackPurchaseParameters](#TrackPurchaseParameters)

```php
$bento->V1->trackPurchase([
  'email' => 'test@bentonow.com',
  'purchaseDetails' => [
    'unique' => [
      'key' => 1234
    ],
    'value' => [
      'amount' => 100,
      'currency' => 'USD'
    ]
  ]
]);
```

---

### `track(parameters: TrackParameters): boolean`

**This TRIGGERS automations!** - There is no way to achieve this same behavior without triggering automations.

Tracks a custom event in Bento.

Because this method uses the batch API, the tag may take between 1 and 3 minutes to appear in the system.

Returns `true` if the event was successfully dispatched. Returns `false` otherwise.

Reference Types: [TrackParameters](#trackparameters)

```php
$bento->V1->track([
  'email' => 'test@bentonow.com',
  'type' => '$custom.event',
  'details' => [
    'fromCustomEvent' => true
  ]
]);
```

## Batch

### `Batch.importSubscribers(parameters: BatchImportSubscribersParameter): number`

**This does not trigger automations!** - If you wish to trigger automations, please batch import events with the type set to `BentoEvents.SUBSCRIBE`, or `$subscribe`. Note that the batch event import cannot attach custom fields and will ignore everything except the email.

Creates a batch job to import subscribers into the system. You can pass in between 1 and 1,000 subscribers to import. Each subscriber must have an email, and may optionally have any additional fields. The additional fields are added as custom fields on the subscriber.

This method is processed by the Bento import queues and it may take between 1 and 5 minutes for the results to appear in your dashboard.

Returns the number of subscribers that were imported.

Reference Types: [BatchImportSubscribersParameter](#batchimportsubscribersparameter)

```php
$bento->V1->Batch->importSubscribers([
  'subscribers' => [
    ['email' => 'test@bentonow.com', 'age' => 21],
    ['email' => 'test2@bentonow.com'],
    ['email' => 'test3@bentonow.com', 'name' => 'Test User']
  ]
]);
```

---

### `Batch.importEvents(parameters: BatchImportEventsParameter): number`

Creates a batch job to import events into the system. You can pass in between 1 and 1,000 events to import. Each event must have an email and a type. In addition to this, you my pass in additional data in the `details` property,

Returns the number of events that were imported.

Reference Types: [BatchImportEventsParameter](#batchimporteventsparameter)

```php
use bentonow\Bento\SDK\Batch\BentoEvents;

$bento->V1->Batch->importEvents([
  'events' => [
    ['email' => 'test@bentonow.com', 'type' => BentoEvents::SUBSCRIBE],
    ['email' => 'test@bentonow.com', 'type' => BentoEvents::UNSUBSCRIBE],
    [
      'email' => 'test@bentonow.com',
      'details' => [
        'customData' => 'Used internally.'
      ],
      'type' => '$custom.myEvent'
    ]
  ]
]);
```

## Commands

### `Commands.addTag(parameters: AddTagParameters): Subscriber | null`

**This does not trigger automations!** - If you wish to trigger automations, please use the core module's `tagSubscriber` method.

Adds a tag to the subscriber with the matching email.

Note that both the tag and the subscriber will be created if either is missing from system.

Reference Types: [AddTagParameters](#AddTagParameters), [Subscriber](#subscriber)

```php
$bento->V1->Commands->addTag([
  'email' => 'test@bentonow.com',
  'tagName' => 'Test Tag'
]);
```

---

### `Commands.removeTag(parameters: RemoveTagParameters): Subscriber | null`

Removes the specified tag from the subscriber with the matching email.

Reference Types: [RemoveTagParameters](#RemoveTagParameters), [Subscriber](#subscriber)

```php
$bento->V1->Commands->removeTag([
  'email' => 'test@bentonow.com',
  'tagName' => 'Test Tag'
]);
```

---

### `Commands.addField(parameters: AddFieldParameters): Subscriber | null`

**This does not trigger automations!** - If you wish to trigger automations, please use the core module's `updateFields` method.

Adds a field to the subscriber with the matching email.

Note that both the field and the subscriber will be created if either is missing from system.

Reference Types: [AddFieldParameters](#addfieldparameters), [Subscriber](#subscriber)

```php
$bento->V1->Commands->addField([
  'email' => 'test@bentonow.com',
  'field' => [
    'key' => 'testKey',
    'value' => 'testValue'
  ]
]);
```

---

### `Commands.removeField(parameters: RemoveFieldParameters): Subscriber | null`

Removes a field to the subscriber with the matching email.

Reference Types: [RemoveFieldParameters](#removefieldparameters), [Subscriber](#subscriber)

```php
$bento->V1->Commands->removeField([
  'email' => 'test@bentonow.com',
  'fieldName' => 'testField'
]);
```

---

### `Commands.subscribe(parameters: SubscribeParameters): Subscriber | null`

**This does not trigger automations!** - If you wish to trigger automations, please use the core module's `addSubscriber` method.

Subscribes the supplied email to Bento. If the email does not exist, it is created.

If the subscriber had previously unsubscribed, they will be re-subscribed.

Reference Types: [SubscribeParameters](#SubscribeParameters), [Subscriber](#subscriber)

```php
$bento->V1->Commands->subscribe([
  'email' => 'test@bentonow.com'
]);
```

---

### `Commands.unsubscribe(parameters: UnsubscribeParameters): Subscriber | null`

**This does not trigger automations!** - If you wish to trigger automations, please use the core module's `removeSubscriber` method.

Unsubscribes the supplied email to Bento. If the email does not exist, it is created and immediately unsubscribed. If they had already unsubscribed, the `unsubscribed_at` property is updated.

Reference Types: [UnsubscribeParameters](#UnsubscribeParameters), [Subscriber](#subscriber)

```php
$bento->V1->Commands->unsubscribe([
  'email' => 'test@bentonow.com',
]);
```

## Experimental

### `Experimental.validateEmail(parameters: ValidateEmailParameters): boolean`

**EXPERIMENTAL** - This functionality is experimental and may change or stop working at any time.

Attempts to validate the email. You can provide additional information to further refine the validation.

If a name is provided, it compares it against the US Census Data, and so the results may be biased.

Reference Types: [ValidateEmailParameters](#ValidateEmailParameters)

```php
$bento->V1->Experimental->validateEmail([
  'email' => 'test@bentonow.com',
]);
```

---

### `Experimental.guessGender(parameters: GuessGenderParameters): GuessGenderResponse`

**EXPERIMENTAL** - This functionality is experimental and may change or stop working at any time.

Attempts to guess the gender of the person given a provided name. It compares the name against the US Census Data, and so the results may be biased.

It is possible for the gender to be unknown if the system cannot confidently conclude what gender it may be.

Reference Types: [GuessGenderParameters](#GuessGenderParameters), [GuessGenderResponse](#GuessGenderResponse)

```php
$bento->V1->Experimental->guessGender([
  'name' => 'Jesse',
]);
```

---

### `Experimental.geolocate(parameters: GeolocateParameters): LocationData | null`

**EXPERIMENTAL** - This functionality is experimental and may change or stop working at any time.

Attempts to provide location data given a provided IP address.

Reference Types: [GeolocateParameters](#GeolocateParameters), [LocationData](#LocationData)

```php
$bento->V1->Experimental->geolocate([
  'ip' => '127.0.0.1',
]);
```

---

### `Experimental.checkBlacklist(parameters: BlacklistParameters): BlacklistResponse`

**EXPERIMENTAL** - This functionality is experimental and may change or stop working at any time.

Looks up the provided URL or IP Address against various blacklists to see if the site has been blacklisted anywhere.

Reference Types: [BlacklistParameters](#BlacklistParameters), [BlacklistResponse](#BlacklistResponse)

```php
$bento->V1->Experimental->checkBlacklist([
  'domain' => 'bentonow.com',
]);
```

## Fields

### `Fields.getFields(): Field[] | null`

Returns all of the fields for the site.

Reference Types: [Field](#Field)

```php
$bento->V1->Fields->getFields();
```

---

### `Fields.createField(parameters: CreateFieldParameters): Field[] | null`

Creates a field inside of Bento. The name of the field is automatically generated from the key that is passed in upon creation. For example:

| Key               | Name              |
| ----------------- | ----------------- |
| `'thisIsAKey'`    | `'This Is A Key'` |
| `'this is a key'` | `'This Is A Key'` |
| `'this-is-a-key'` | `'This Is A Key'` |
| `'this_is_a_key'` | `'This Is A Key'` |

Reference Types: [CreateFieldParameters](#CreateFieldParameters), [Field](#Field)

```php
$bento->V1->Fields->createField([
  'key' => 'testKey'
]);
```

## Forms

### `Forms.getResponses(formIdentifier: string): FormResponse[] | null`

Returns all of the responses for the form with the specified identifier.

Reference Types: [FormResponse](#FormResponse)

```php
$bento->V1->Forms->getResponses('test-formid-1234');
```

## Subscribers

### `Subscribers.getSubscribers(parameters?: GetSubscribersParameters): Subscriber | null`

Returns the subscriber with the specified email or UUID.

Reference Types: [GetSubscribersParameters](#GetSubscribersParameters), [Subscriber](#subscriber)

```php
$bento->V1->Subscribers->getSubscribers([
  'uuid' => '1234'
]);

$bento->V1->Subscribers->getSubscribers([
  'email' => 'test@bentonow.com'
]);
```

---

### `Subscribers.createSubscriber(parameters: CreateSubscriberParameters): Subscriber | null`

Creates a subscriber inside of Bento.

Reference Types: [CreateSubscriberParameters](#CreateSubscriberParameters), [Subscriber](#subscriber)

```php
$bento->V1->Subscribers->createSubscriber([
  'email' => 'test@bentonow.com'
]);
```

## Tags

### `Tags.getTags(): Tag[] | null`

Returns all of the fields for the site.

Reference Types: [Tag](#Tag)

```php
$bento->V1->Tags->getTags();
```

---

### `Tags.createTag(parameters: CreateTagParameters): Tag[] | null`

Creates a tag inside of Bento.

Reference Types: [Tag](#Tag)

```php
$bento->V1->Tags->createTag([
  'name' => 'test tag'
]);
```

## Types Reference

### `AddFieldParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |
| field    | `mixed`  | _none_  | ✔️       |

---

### `AddSubscriberParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| date     | `Date`   | _none_  | ❌       |
| email    | `string` | _none_  | ✔️       |
| fields   | `mixed`  | _none_  | ❌       |

---

### `AddTagParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |
| tagName  | `string` | _none_  | ✔️       |

---

### `BatchImportEventsParameter`

| Property | Type                          | Default | Required |
| -------- | ----------------------------- | ------- | -------- |
| events   | [`BentoEvent[]`](#BentoEvent) | _none_  | ✔️       |

---

### `BatchImportSubscribersParameter`

| Property    | Type                            | Default | Required |
| ----------- | ------------------------------- | ------- | -------- |
| subscribers | `({ email: string } & mixed)[]` | _none_  | ✔️       |

---

### `BentoEvent`

This type is a discriminated union of a few different types. Each of these types are documented below:

#### `BaseEvent`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| date     | `Date`   | _none_  | ❌       |
| details  | `mixed`  | _none_  | ✔️       |
| email    | `string` | _none_  | ✔️       |
| type     | `string` | _none_  | ✔️       |

#### `PurchaseEvent`

| Property | Type                                    | Default | Required |
| -------- | --------------------------------------- | ------- | -------- |
| date     | `Date`                                  | _none_  | ❌       |
| details  | [`PurchaseDetails`](#PurchaseDetails)   | _none_  | ✔️       |
| email    | `string`                                | _none_  | ✔️       |
| type     | `BentoEvents.PURCHASE` \| `'$purchase'` | _none_  | ✔️       |

#### `SubscribeEvent`

| Property | Type                                      | Default | Required |
| -------- | ----------------------------------------- | ------- | -------- |
| date     | `Date`                                    | _none_  | ❌       |
| email    | `string`                                  | _none_  | ✔️       |
| fields   | `mixed`                                   | _none_  | ❌       |
| type     | `BentoEvents.SUBSCRIBE` \| `'$subscribe'` | _none_  | ✔️       |

#### `TagEvent`

| Property | Type                          | Default | Required |
| -------- | ----------------------------- | ------- | -------- |
| date     | `Date`                        | _none_  | ❌       |
| details  | `{ tag: string }`             | _none_  | ✔️       |
| email    | `string`                      | _none_  | ✔️       |
| type     | `BentoEvents.TAG` \| `'$tag'` | _none_  | ✔️       |

#### `UnsubscribeEvent`

| Property | Type                                          | Default | Required |
| -------- | --------------------------------------------- | ------- | -------- |
| date     | `Date`                                        | _none_  | ❌       |
| email    | `string`                                      | _none_  | ✔️       |
| type     | `BentoEvents.UNSUBSCRIBE` \| `'$unsubscribe'` | _none_  | ✔️       |

#### `UpdateFieldsEvent`

| Property | Type                                              | Default | Required |
| -------- | ------------------------------------------------- | ------- | -------- |
| date     | `Date`                                            | _none_  | ❌       |
| email    | `string`                                          | _none_  | ✔️       |
| type     | `BentoEvents.UPDATE_FIELDS` \| `'$update_fields'` | _none_  | ✔️       |
| fields   | `mixed`                                           | _none_  | ✔️       |

---

### `BlacklistParameters`

Note that this takes either `domain` _or_ `ip`, but never both.

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| domain   | `string` | _none_  | ✔️       |

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| ip       | `string` | _none_  | ✔️       |

---

### `BlacklistResponse`

The results is an object where the key is the name of the blacklist that was checked, and the value is whether or not the domain/IP appeared on that blacklist.

| Property    | Type     | Default | Required |
| ----------- | -------- | ------- | -------- |
| description | `string` | _none_  | ✔️       |
| query       | `string` | _none_  | ✔️       |
| results     | `mixed`  | _none_  | ✔️       |

---

### `BrowserData`

| Property   | Type     | Default | Required |
| ---------- | -------- | ------- | -------- |
| height     | `string` | _none_  | ✔️       |
| user_agent | `string` | _none_  | ✔️       |
| width      | `string` | _none_  | ✔️       |

---

### `CreateFieldParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| key      | `string` | _none_  | ✔️       |

---

### `CreateSubscriberParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |

### `CreateTagParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| name     | `string` | _none_  | ✔️       |

---

### `EntityType`

This is an enum with the following values:

| Name            | Value               |
| --------------- | ------------------- |
| EVENTS          | `'events'`          |
| TAGS            | `'tags'`            |
| VISITORS        | `'visitors'`        |
| VISITORS_FIELDS | `'visitors-fields'` |

---

### `Field`

| Property   | Type                                        | Default | Required |
| ---------- | ------------------------------------------- | ------- | -------- |
| attributes | [`FieldAttributes`](#FieldAttributes)       | _none_  | ✔️       |
| id         | `string`                                    | _none_  | ✔️       |
| type       | [`EntityType.VISITORS_FIELDS`](#EntityType) | _none_  | ✔️       |

---

### `FieldAttributes`

| Property    | Type                | Default | Required |
| ----------- | ------------------- | ------- | -------- |
| created_at  | `string`            | _none_  | ✔️       |
| key         | `string`            | _none_  | ✔️       |
| name        | `string`            | _none_  | ✔️       |
| whitelisted | `boolean` \| `null` | _none_  | ✔️       |

---

### `FormResponse`

| Property   | Type                                                | Default | Required |
| ---------- | --------------------------------------------------- | ------- | -------- |
| attributes | [`FormResponseAttributes`](#FormResponseAttributes) | _none_  | ✔️       |
| id         | `string`                                            | _none_  | ✔️       |
| type       | [`EntityType.EVENTS`](#EntityType)                  | _none_  | ✔️       |

---

### `FormResponseAttributes`

| Property | Type                                    | Default | Required |
| -------- | --------------------------------------- | ------- | -------- |
| data     | [`FormResponseData`](#FormResponseData) | _none_  | ✔️       |
| uuid     | `string`                                | _none_  | ✔️       |

---

### `FormResponseData`

| Property | Type                            | Default | Required |
| -------- | ------------------------------- | ------- | -------- |
| browser  | [`BrowserData`](#BrowserData)   | _none_  | ✔️       |
| date     | `string`                        | _none_  | ✔️       |
| details  | `mixed`                         | _none_  | ✔️       |
| fields   | `mixed`                         | _none_  | ✔️       |
| id       | `string`                        | _none_  | ✔️       |
| identity | [`IdentityData`](#IdentityData) | _none_  | ✔️       |
| ip       | `string`                        | _none_  | ✔️       |
| location | [`LocationData`](#LocationData) | _none_  | ✔️       |
| page     | [`PageData`](#PageData)         | _none_  | ✔️       |
| site     | `string`                        | _none_  | ✔️       |
| type     | `string`                        | _none_  | ✔️       |
| visit    | `string`                        | _none_  | ✔️       |
| visitor  | `string`                        | _none_  | ✔️       |

---

### `GetSubscribersParameters`

Note that this takes either `email` _or_ `uuid`, but never both.

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| uuid     | `string` | _none_  | ✔️       |

---

### `GeolocateParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| ip       | `string` | _none_  | ✔️       |

---

### `GuessGenderParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| name     | `string` | _none_  | ✔️       |

---

### `GuessGenderResponse`

| Property   | Type               | Default | Required |
| ---------- | ------------------ | ------- | -------- |
| confidence | `number` \| `null` | _none_  | ✔️       |
| gender     | `string` \| `null` | _none_  | ✔️       |

---

### `IdentityData`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |

---

### `LocationData`

| Property         | Type     | Default | Required |
| ---------------- | -------- | ------- | -------- |
| city_name        | `string` | _none_  | ❌       |
| continent_code   | `string` | _none_  | ❌       |
| country_code2    | `string` | _none_  | ❌       |
| country_code3    | `string` | _none_  | ❌       |
| country_name     | `string` | _none_  | ❌       |
| ip               | `string` | _none_  | ❌       |
| latitude         | `number` | _none_  | ❌       |
| longitude        | `number` | _none_  | ❌       |
| postal_code      | `string` | _none_  | ❌       |
| real_region_name | `string` | _none_  | ❌       |
| region_name      | `string` | _none_  | ❌       |
| request          | `string` | _none_  | ❌       |

---

### `PageData`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| host     | `string` | _none_  | ✔️       |
| path     | `string` | _none_  | ✔️       |
| protocol | `string` | _none_  | ✔️       |
| referrer | `string` | _none_  | ✔️       |
| url      | `string` | _none_  | ✔️       |

---

### `PurchaseCart`

| Property               | Type                              | Default | Required |
| ---------------------- | --------------------------------- | ------- | -------- |
| abandoned_checkout_url | `string`                          | _none_  | ❌       |
| items                  | [`PurchaseItem[]`](#PurchaseItem) | _none_  | ❌       |

---

### `PurchaseDetails`

| Property | Type                                    | Default | Required |
| -------- | --------------------------------------- | ------- | -------- |
| unique   | `{ key: string \| number; }`            | _none_  | ✔️       |
| value    | `{ currency: string; amount: number; }` | _none_  | ✔️       |
| cart     | [`PurchaseCart`](#PurchaseCart)         | _none_  | ❌       |

---

### `PurchaseItem`

In addition to the properties below, you can pass any other properties that you want as part of the `PurchaseItem`.

| Property      | Type     | Default | Required |
| ------------- | -------- | ------- | -------- |
| product_sku   | `string` | _none_  | ❌       |
| product_name  | `string` | _none_  | ❌       |
| quantity      | `number` | _none_  | ❌       |
| product_price | `number` | _none_  | ❌       |
| product_id    | `string` | _none_  | ❌       |

---

### `RemoveFieldParameters`

| Property  | Type     | Default | Required |
| --------- | -------- | ------- | -------- |
| email     | `string` | _none_  | ✔️       |
| fieldName | `string` | _none_  | ✔️       |

---

### `RemoveSubscriberParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| date     | `Date`   | _none_  | ❌       |
| email    | `string` | _none_  | ✔️       |

---

### `RemoveTagParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |
| tagName  | `string` | _none_  | ✔️       |

---

### `SubscribeParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |

---

### `Subscriber`

| Property   | Type                                            | Default | Required |
| ---------- | ----------------------------------------------- | ------- | -------- |
| attributes | [`SubscriberAttributes`](#subscriberattributes) | _none_  | ✔️       |
| id         | `string`                                        | _none_  | ✔️       |
| type       | [`EntityType.VISITOR`](#EntityType)             | _none_  | ✔️       |

### `SubscriberAttributes`

| Property        | Type              | Default | Required |
| --------------- | ----------------- | ------- | -------- |
| cached_tag_ids  | `string[]`        | _none_  | ✔️       |
| email           | `string`          | _none_  | ✔️       |
| fields          | `mixed` \| `null` | _none_  | ✔️       |
| unsubscribed_at | `string`          | _none_  | ✔️       |
| uuid            | `string`          | _none_  | ✔️       |

### `Tag`

| Property     | Type               | Default | Required |
| ------------ | ------------------ | ------- | -------- |
| created_at   | `string`           | _none_  | ✔️       |
| discarded_at | `string` \| `null` | _none_  | ✔️       |
| name         | `string` \| `null` | _none_  | ✔️       |
| site_id      | `string`           | _none_  | ✔️       |

---

### `TagAttributes`

| Property   | Type                              | Default | Required |
| ---------- | --------------------------------- | ------- | -------- |
| attributes | [`TagAttributes`](#TagAttributes) | _none_  | ✔️       |
| id         | `string`                          | _none_  | ✔️       |
| type       | [`EntityType.TAG`](#EntityType)   | _none_  | ✔️       |

---

### `TagSubscriberParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| date     | `Date`   | _none_  | ❌       |
| email    | `string` | _none_  | ✔️       |
| tagName  | `string` | _none_  | ✔️       |

---

### `TrackParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |
| type     | `string` | _none_  | ✔️       |
| details  | `mixed`  | _none_  | ❌       |

---

### `TrackPurchaseParameters`

| Property        | Type                                  | Default | Required |
| --------------- | ------------------------------------- | ------- | -------- |
| date            | `Date`                                | _none_  | ❌       |
| email           | `string`                              | _none_  | ✔️       |
| purchaseDetails | [`PurchaseDetails`](#PurchaseDetails) | _none_  | ✔️       |

---

### `UnsubscribeParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| email    | `string` | _none_  | ✔️       |

---

### `UpdateFieldsParameters`

| Property | Type     | Default | Required |
| -------- | -------- | ------- | -------- |
| date     | `Date`   | _none_  | ❌       |
| email    | `string` | _none_  | ✔️       |
| fields   | `mixed`  | _none_  | ✔️       |

---

### `ValidateEmailParameters`

| Property  | Type     | Default | Required |
| --------- | -------- | ------- | -------- |
| email     | `string` | _none_  | ✔️       |
| ip        | `string` | _none_  | ❌       |
| name      | `string` | _none_  | ❌       |
| userAgent | `string` | _none_  | ❌       |

## Things to know

1. Tracking: All events must be identified. Anonymous support coming soon!
2. Tracking: Most events and indexed inside Bento within a few seconds.
3. If you need support, just let us know!

## Contributing

Bug reports and pull requests are welcome on GitHub at https://github.com/bentonow/bento-php-sdk. This project is intended to be a safe, welcoming space for collaboration, and contributors are expected to adhere to the [Contributor Covenant](http://contributor-covenant.org) code of conduct.

## License

The package is available as open source under the terms of the MIT License.
