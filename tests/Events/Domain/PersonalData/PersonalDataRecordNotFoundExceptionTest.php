<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Events\Domain\PersonalData;

use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordId;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordNotFoundException;

$id = PersonalDataRecordId::next();

\test('forId returns exception with message including the passed id')
    ->expect(fn() => PersonalDataRecordNotFoundException::forId($id))
    ->getMessage()
    ->toBe("Personal data record with id '{$id->asString()}' could not be found");
