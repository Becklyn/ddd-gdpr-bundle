<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Tests\Domain;

use Becklyn\Ddd\PersonalData\Domain\PersonalDataRecordId;
use Webmozart\Assert\InvalidArgumentException;

\test('next returns PersonalDataRecordId')
    ->expect(fn() => PersonalDataRecordId::next())
    ->toBeInstanceOf(PersonalDataRecordId::class);

\test('fromString returns PersonalDataRecordId with string representation equal to passed uuid')
    ->expect(fn() => PersonalDataRecordId::fromString('2da18b9d-d78f-41fb-9633-2048f6ed56ff')->asString())
    ->toBe('2da18b9d-d78f-41fb-9633-2048f6ed56ff');

\test('fromString does not accept non-uuid values', function() : void {
    \expect(fn() => PersonalDataRecordId::fromString('notAUuid'))
        ->toThrow(InvalidArgumentException::class);
});
