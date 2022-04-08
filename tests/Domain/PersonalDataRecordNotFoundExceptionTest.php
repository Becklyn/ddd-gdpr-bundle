<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Tests\Domain;

use Becklyn\Ddd\PersonalData\Domain\PersonalDataRecordId;
use Becklyn\Ddd\PersonalData\Domain\PersonalDataRecordNotFoundException;

$id = PersonalDataRecordId::next();

\test('forId returns exception with message including the passed id')
    ->expect(fn() => PersonalDataRecordNotFoundException::forId($id))
    ->getMessage()
    ->toBe("Personal data record with id '{$id->asString()}' could not be found");
