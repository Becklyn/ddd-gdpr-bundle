<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Events\Domain\PersonalData;

use Becklyn\Ddd\Events\Domain\PersonalData\PersonalData;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDate;

$date = new \DateTimeImmutable();

\test('PersonalDate::create returns PersonalDate with string personal value in Y-m-d H:i:s.u format')
    ->expect(fn() => PersonalDate::create($date))
    ->personalValueAsString()
    ->toBe($date->format('Y-m-d H:i:s.u'));

\test('PersonalDate::personalValue returns value passed to create')
    ->expect(fn() => PersonalDate::create($date))
    ->personalValue()
    ->toEqual($date);

\test('PersonalDate::personalValue returns DateTimeImmutable for 0001-01-01 00:00:00.000000 if personal value is anonymized', function () : void {
    $personalDate = PersonalDate::create(new \DateTimeImmutable());

    $reflectionObject = new \ReflectionObject($personalDate);
    $reflectionProperty = $reflectionObject->getProperty('personalValue');
    $reflectionProperty->setAccessible(true);
    $reflectionProperty->setValue($personalDate, PersonalData::ANONYMIZED_STRING);

    \expect($personalDate->personalValueAsString())
        ->toBe(PersonalData::ANONYMIZED_STRING);

    \expect($personalDate->personalValue())
        ->toEqual(new \DateTimeImmutable('0001-01-01 00:00:00.000000'));
});
