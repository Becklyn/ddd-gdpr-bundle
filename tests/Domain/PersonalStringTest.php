<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Tests\Domain;

use Becklyn\Ddd\PersonalData\Domain\PersonalString;

\test('PersonalString::personalValue returns value passed to create')
    ->expect(fn() => PersonalString::create('test'))
    ->personalValue()
    ->toBe('test');
