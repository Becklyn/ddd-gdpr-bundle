<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Tests\Infrastructure\EventStore\Symfony;

use Becklyn\Ddd\Events\Domain\EventId;
use Becklyn\Ddd\PersonalData\Domain\PersonalDataRecordId;
use Becklyn\Ddd\PersonalData\Domain\PersonalDataStore;
use Becklyn\Ddd\PersonalData\Domain\PersonalNullableString;
use Becklyn\Ddd\PersonalData\Infrastructure\EventStore\Symfony\SymfonyGdprCompliantEventSerializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\SerializerInterface;

\test('serialize stores personal values in the personal data store and replaces them with personal data record ids', function() : void {
    $event = new DomainEventProxy(EventId::fromString(Uuid::uuid4()->toString()), new \DateTimeImmutable());

    $symfonySerializer = \mock(SerializerInterface::class)
        ->shouldReceive('serialize')
        ->with($event, 'json')
        ->once()
        ->andReturn('{"antragsnummer":"asdasdasd","name":{"daysToLive":30,"personalValue":"Max Musterman"},"dateOfBirth":{"daysToLive":30,"personalValue":"2022-04-06 14:37:31.477757"},"antragId":{"id":"f25e227e-045c-4b62-9961-6f0e342945a1"},"id":{"id":"9a72afda-0391-419f-bf9d-ab49cc49eb64"},"raisedTs":"2022-04-06 14:37:32.844783"}');

    $personalDataRecordId1 = PersonalDataRecordId::next();
    $personalDataRecordId2 = PersonalDataRecordId::next();

    $personalDataStore = \mock(PersonalDataStore::class)
        ->shouldReceive('storePersonalData')
        ->andReturnUsing(function(PersonalNullableString $data) use ($personalDataRecordId1, $personalDataRecordId2) {
            return 'Max Musterman' === $data->personalValue() ? $personalDataRecordId1 : $personalDataRecordId2;
        });

    $fixture = new SymfonyGdprCompliantEventSerializer($symfonySerializer->getMock(), $personalDataStore->getMock());

    \expect($fixture->serialize($event, 'json'))->toBe('{"antragsnummer":"asdasdasd","name":{"daysToLive":30,"personalValue":"' . $personalDataRecordId1->asString() . '"},"dateOfBirth":{"daysToLive":30,"personalValue":"' . $personalDataRecordId2->asString() . '"},"antragId":{"id":"f25e227e-045c-4b62-9961-6f0e342945a1"},"id":{"id":"9a72afda-0391-419f-bf9d-ab49cc49eb64"},"raisedTs":"2022-04-06 14:37:32.844783"}');
});

\test('serialize returns result of symfony serializer if passed data is not a domain event', function() : void {
    $data = 'foo';

    $symfonySerializer = \mock(SerializerInterface::class)
        ->shouldReceive('serialize')
        ->with($data, 'json', [])
        ->once()
        ->andReturn('bar');

    $personalDataStore = \mock(PersonalDataStore::class)->shouldNotReceive('storePersonalData');

    $fixture = new SymfonyGdprCompliantEventSerializer($symfonySerializer->getMock(), $personalDataStore->getMock());

    \expect($fixture->serialize($data, 'json'))->toBe('bar');
});

\test('deserialize loads personal values from personal data store and injects them into serialized event in place of personal data record ids', function() : void {
    $personalDataRecordId1 = PersonalDataRecordId::next();
    $personalDataRecordId2 = PersonalDataRecordId::next();
    $serializedEvent = '{"antragsnummer":"asdasdasd","name":{"daysToLive":30,"personalValue":"' . $personalDataRecordId1->asString() . '"},"dateOfBirth":{"daysToLive":30,"personalValue":"' . $personalDataRecordId2->asString() . '"},"antragId":{"id":"f25e227e-045c-4b62-9961-6f0e342945a1"},"id":{"id":"9a72afda-0391-419f-bf9d-ab49cc49eb64"},"raisedTs":"2022-04-06 14:37:32.844783"}';

    $personalDataStore = \mock(PersonalDataStore::class)
        ->shouldReceive('loadPersonalValue')
        ->andReturnUsing(function(PersonalDataRecordId $personalDataRecordId) use ($personalDataRecordId1, $personalDataRecordId2) {
            return $personalDataRecordId->asString() === $personalDataRecordId1->asString() ? 'Max Musterman' : '2022-04-06 14:37:31.477757';
        });

    $event = new DomainEventProxy(EventId::fromString(Uuid::uuid4()->toString()), new \DateTimeImmutable());
    $symfonySerializer = \mock(SerializerInterface::class)
        ->shouldReceive('deserialize')
        ->with(
            '{"antragsnummer":"asdasdasd","name":{"daysToLive":30,"personalValue":"Max Musterman"},"dateOfBirth":{"daysToLive":30,"personalValue":"2022-04-06 14:37:31.477757"},"antragId":{"id":"f25e227e-045c-4b62-9961-6f0e342945a1"},"id":{"id":"9a72afda-0391-419f-bf9d-ab49cc49eb64"},"raisedTs":"2022-04-06 14:37:32.844783"}',
            DomainEventProxy::class,
            'json',
            []
        )
        ->once()
        ->andReturn($event);

    $fixture = new SymfonyGdprCompliantEventSerializer($symfonySerializer->getMock(), $personalDataStore->getMock());

    \expect($fixture->deserialize($serializedEvent, DomainEventProxy::class, 'json'))->toBe($event);
});
