# Becklyn DDD GDPR Bundle

This Symfony bundle integrates with the Becklyn DDD framework to allow for anonymizing personal information stored in the event store as required by GDPR or other privacy regulations.

## Installation

- Run `composer require becklyn/ddd-gdpr-bundle`
- Add the following to bundles.php:
```
Becklyn\Ddd\PersonalData\BecklynDddGdprBundle::class => ['all' => true],
```
- There is a doctrine migration provided. Execute it by running `php bin/console doctrine:migrations:migrate`

## How To

Personal values, in other words data which needs to be anonymized in the event store, must be passed to events as objects of classes extending the `Becklyn\Ddd\PersonalData\Domain\AbstractPersonalData` class. Such classes for storing strings and datetime values are already provided:

- `Becklyn\Ddd\PersonalData\Domain\PersonalDate`
- `Becklyn\Ddd\PersonalData\Domain\PersonalNullableDate` 
- `Becklyn\Ddd\PersonalData\Domain\PersonalNullableString`
- `Becklyn\Ddd\PersonalData\Domain\PersonalString`

The bundle will replace values contained in these objects with a reference pointing to the `personal_data_store` DB table, where the actual values are stored, just before persisting events in the event store. Note that the event objects themselves won't be changed, only their serialized form is manipulated. If you are passing the events to other parts of the application after they get saved in the event store, they will still contain the real data.

Similarly, the bundle will restore the values stored in `personal_data_store` into the event objects as they are loaded via `EventStore::getAggregateStream`.

### Anonymizing

Currently, the bundle only anonymizes expired personal data at the time of loading the events from the event store. This will only be persisted to the database if your application flushes the entity manager during the request, after the events have been loaded. This is intended as a fallback so that no expired personal data exits the event store; we recommend implementing your own mechanism for reliably anonymizing personal data at the correct times.

This must be done by replacing the personal values stored in the `personal_data_store` table with the value of the `PersonalData::ANONYMIZED_STRING` constant, which at the time of this writing is `ANONYMIZED`. Expiry times for personal data are stored in the `personal_data_store` table with every record.

### Handling anonymized data

As mentioned, anonymized data will have the value `ANONYMIZED` in its internal string representation. For `PersonalDate` and `PersonalNullableDate`, which return `\DateTimeImmutable` objects as their value, we have decided to represent this with the date of 0001-01-01 00:00:00.000000. It is your responsibility to handle this data as you deem appropriate in your application.

The value `null` has not been chosen to represent anonymized data because it can be a valid, non-anonymized value.