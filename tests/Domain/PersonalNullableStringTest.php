<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Tests\Domain;

use Becklyn\Ddd\PersonalData\Domain\PersonalNullableString;

\test('PersonalNullableString::personalValue returns value passed to create', function($value) : void {
    \expect(PersonalNullableString::create($value))
        ->personalValue()
        ->toBe($value);
})->with(['test', null]);
