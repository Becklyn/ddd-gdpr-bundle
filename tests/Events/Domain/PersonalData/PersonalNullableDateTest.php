<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Events\Domain\PersonalData;

use Becklyn\Ddd\Events\Domain\PersonalData\PersonalData;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalNullableDate;

$date = new \DateTimeImmutable();

\test('PersonalNullableDate::create returns PersonalDate with string personal value in Y-m-d H:i:s.u format')
    ->expect(fn() => PersonalNullableDate::create($date))
    ->personalValueAsString()
    ->toBe($date->format('Y-m-d H:i:s.u'));

\test('PersonalNullableDate::personalValue returns value passed to create', function($value) : void {
    \expect(PersonalNullableDate::create($value))
        ->personalValue()
        ->toEqual($value);
})->with([$date, null]);

\test('PersonalNullableDate::personalValue returns DateTimeImmutable for 0001-01-01 00:00:00.000000 if personal value is anonymized', function () : void {
    $personalDate = PersonalNullableDate::create(new \DateTimeImmutable());

    $reflectionObject = new \ReflectionObject($personalDate);
    $reflectionProperty = $reflectionObject->getProperty('personalValue');
    $reflectionProperty->setAccessible(true);
    $reflectionProperty->setValue($personalDate, PersonalData::ANONYMIZED_STRING);

    \expect($personalDate->personalValueAsString())
        ->toBe(PersonalData::ANONYMIZED_STRING);

    \expect($personalDate->personalValue())
        ->toEqual(new \DateTimeImmutable('0001-01-01 00:00:00.000000'));
});
