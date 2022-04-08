<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Tests\Infrastructure\Domain\Doctrine;

use Becklyn\Ddd\PersonalData\Domain\PersonalData;
use Becklyn\Ddd\PersonalData\Domain\PersonalDataRecordId;
use Becklyn\Ddd\PersonalData\Domain\PersonalString;
use Becklyn\Ddd\PersonalData\Infrastructure\Domain\Doctrine\DoctrineStoredPersonalDataRecord;

\it('stores personal value from passed PersonalData as string')
    ->expect(fn() => new DoctrineStoredPersonalDataRecord(
        PersonalDataRecordId::next(),
        PersonalString::create('test'),
        \getDomainEventMock()
    ))->personalValue()->toBe('test');

\it('stores days to live from passed PersonalData')
    ->expect(fn() => new DoctrineStoredPersonalDataRecord(
        PersonalDataRecordId::next(),
        PersonalString::create('test', 55),
        \getDomainEventMock()
    ))->daysToLive()->toBe(55);

$id = PersonalDataRecordId::next();
\it('stores passed PersonalDataRecordId')
    ->expect(fn() => new DoctrineStoredPersonalDataRecord(
        $id,
        PersonalString::create('test'),
        \getDomainEventMock()
    ))->id()->toEqual($id);

\it('stores AggregateId from passed parent domain event')
    ->expect(fn() => new DoctrineStoredPersonalDataRecord(
        $id,
        PersonalString::create('test'),
        \getDomainEventMock(aggregateIdAsString: 'aggregateid')
    ))->aggregateId()->toBe('aggregateid');

$raisedTs = new \DateTimeImmutable();
\it('stores raisedTs from passed parent domain event')
    ->expect(fn() => new DoctrineStoredPersonalDataRecord(
        $id,
        PersonalString::create('test'),
        \getDomainEventMock(raisedTs: $raisedTs)
    ))->eventRaisedTs()->toEqual($raisedTs);

\test('expiresTs is equal to raisedTs plus daysToLive')
    ->expect(fn() => new DoctrineStoredPersonalDataRecord(
        $id,
        PersonalString::create('test', 22),
        \getDomainEventMock(raisedTs: $raisedTs)
    ))->expiresTs()->toEqual($raisedTs->add(\DateInterval::createFromDateString("22 days")));

\test('anonymizedTs is null if record has not been anonymized')
    ->expect(fn() => new DoctrineStoredPersonalDataRecord(
        PersonalDataRecordId::next(),
        PersonalString::create('test'),
        \getDomainEventMock()
    ))->anonymizedTs()->toBeNull()
        ->isAnonymized()->toBeFalse();

\test('anonymizedTs is not null if record has been anonymized manually')
    ->expect(function() {
        $record = new DoctrineStoredPersonalDataRecord(
            PersonalDataRecordId::next(),
            PersonalString::create('test'),
            \getDomainEventMock()
        );
        $record->anonymize();
        return $record;
    })->anonymizedTs()->not()->toBeNull()
    ->isAnonymized()->toBeTrue();

\test('anonymizedTs is not null if record has been anonymized by fetching personal value with expired date of anonymization')
    ->expect(function() {
        $record = new DoctrineStoredPersonalDataRecord(
            PersonalDataRecordId::next(),
            PersonalString::create('test', -10),
            \getDomainEventMock()
        );
        $record->personalValue();
        return $record;
    })->anonymizedTs()->not()->toBeNull()
    ->isAnonymized()->toBeTrue();

\test('anonymizedTs is not set if record has already been anonymized', function() : void {
    $record = new DoctrineStoredPersonalDataRecord(
        PersonalDataRecordId::next(),
        PersonalString::create('test'),
        \getDomainEventMock()
    );
    $record->anonymize();

    $originalAnonymizedTs = $record->anonymizedTs();
    $record->anonymize();
    \expect($record->anonymizedTs())->toBe($originalAnonymizedTs);
});

\it('returns PersonalData::ANONYMIZED_STRING if it has been anonymized manually')
    ->expect(function() {
        $record = new DoctrineStoredPersonalDataRecord(
            PersonalDataRecordId::next(),
            PersonalString::create('test'),
            \getDomainEventMock()
        );
        $record->anonymize();
        return $record;
    })->personalValue()->toBe(PersonalData::ANONYMIZED_STRING);

\it('returns PersonalData::ANONYMIZED_STRING if it has not been anonymized manually but date of anonymization has expired')
    ->expect(fn() => new DoctrineStoredPersonalDataRecord(
        PersonalDataRecordId::next(),
        PersonalString::create('test', -10),
        \getDomainEventMock()
    ))->personalValue()->toBe(PersonalData::ANONYMIZED_STRING);
