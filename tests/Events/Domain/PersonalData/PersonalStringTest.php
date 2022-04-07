<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Events\Domain\PersonalData;

use Becklyn\Ddd\Events\Domain\PersonalData\PersonalString;

\test('PersonalString::personalValue returns value passed to create')
    ->expect(fn() => PersonalString::create('test'))
    ->personalValue()
    ->toBe('test');
