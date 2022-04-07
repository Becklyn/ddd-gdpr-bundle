<?php declare(strict_types=1);

namespace Becklyn\Ddd\Tests\Events\Domain\PersonalData;

use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordId;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordNotFoundException;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalString;
use Becklyn\Ddd\Events\Infrastructure\PersonalData\Doctrine\DoctrinePersonalDataStore;
use Becklyn\Ddd\Events\Infrastructure\PersonalData\Doctrine\DoctrineStoredPersonalDataRecord;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

\test('storePersonalData persists DoctrineStoredPersonalDataRecord in entity manager with values from passed personal data and event', function () : void {
    $personalValueAsString = 'test';
    $daysToLive = 55;

    $aggregateIdAsString = 'fooBar';
    $parentEventRaisedTs = new \DateTimeImmutable();

    $em = \mock(EntityManagerInterface::class)
        ->shouldReceive('persist')
        /** @var DoctrineStoredPersonalDataRecord $actualPersonalData */
        ->with(\Mockery::capture($actualPersonalData))
        ->once();

    $fixture = new DoctrinePersonalDataStore($em->getMock());

    $fixture->storePersonalData(
        PersonalString::create($personalValueAsString, $daysToLive),
        \getDomainEventMock($aggregateIdAsString, $parentEventRaisedTs)
    );

    \expect($actualPersonalData)
        ->toBeInstanceOf(DoctrineStoredPersonalDataRecord::class)
        ->personalValue()->toBe($personalValueAsString)
        ->daysToLive()->toBe($daysToLive)
        ->aggregateId()->toBe($aggregateIdAsString)
        ->eventRaisedTs()->toBe($parentEventRaisedTs);
});

\test('storePersonalData returns same PersonalDataRecordId used for DoctrineStoredPersonalDataRecord persisted in entity manager', function () : void {
    $em = \mock(EntityManagerInterface::class)
        ->shouldReceive('persist')
        /** @var DoctrineStoredPersonalDataRecord $actualPersonalData */
        ->with(\Mockery::capture($actualPersonalData))
        ->once();

    $fixture = new DoctrinePersonalDataStore($em->getMock());

    \expect(
        $fixture->storePersonalData(PersonalString::create('test'), \getDomainEventMock('fooBar'))
    )
        ->asString()->toBe($actualPersonalData->id()->asString());
});

\test('loadPersonalValue returns personal value of record found in DoctrineStoredPersonalDataRecord doctrine repository by given PersonalDataRecordId', function () : void {
    $personalValue = 'test';

    $recordId = PersonalDataRecordId::next();
    $recordIdAsString = $recordId->asString();
    $record = \mock(DoctrineStoredPersonalDataRecord::class)->expect(
        personalValue: fn() => $personalValue
    );


    $doctrineRepository = \mock(EntityRepository::class)->expect(
        find: fn ($recordIdAsString) => $record
    );
    $repoClass = DoctrineStoredPersonalDataRecord::class;
    $em = \mock(EntityManagerInterface::class)->expect(
        getRepository: fn($repoClass) => $doctrineRepository,
    );

    $fixture = new DoctrinePersonalDataStore($em);

    \expect($fixture->loadPersonalValue($recordId))->toBe($personalValue);
});

\test('loadPersonalValue throws PersonalDataRecordNotFoundException if DoctrineStoredPersonalDataRecord can not be found in doctrine repository by given PersonalDataRecordId', function () : void {
    $recordId = PersonalDataRecordId::next();
    $recordIdAsString = $recordId->asString();

    $doctrineRepository = \mock(EntityRepository::class)->expect(
        find: fn ($recordIdAsString) => null
    );
    $repoClass = DoctrineStoredPersonalDataRecord::class;
    $em = \mock(EntityManagerInterface::class)->expect(
        getRepository: fn($repoClass) => $doctrineRepository,
    );

    $fixture = new DoctrinePersonalDataStore($em);

    \expect(fn() => $fixture->loadPersonalValue($recordId))->toThrow(PersonalDataRecordNotFoundException::class);
});
