<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Events\Domain\PersonalData;

\test('daysToLive returns value passed to constructor')
    ->expect(fn() => IntegerAbstractPersonalDataTestProxy::create(1, 60))
    ->daysToLive()
    ->toBe(60);

\test('personalValueAsString returns string representation of non-null personal value')
    ->expect(fn() => IntegerAbstractPersonalDataTestProxy::create(5))
    ->personalValueAsString()
    ->toBe('5');

\test('personalValueAsString returns null for null personal value')
    ->expect(fn() => IntegerAbstractPersonalDataTestProxy::create(null))
    ->personalValueAsString()
    ->toBeNull();
