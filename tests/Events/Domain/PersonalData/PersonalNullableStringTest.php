<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Events\Domain\PersonalData;

use Becklyn\Ddd\Events\Domain\PersonalData\PersonalNullableString;

\test('PersonalNullableString::personalValue returns value passed to create', function($value) : void {
    \expect(PersonalNullableString::create($value))
        ->personalValue()
        ->toBe($value);
})->with(['test', null]);
