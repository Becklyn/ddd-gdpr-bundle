<?php declare(strict_types=1);

namespace Becklyn\Ddd\Events\Infrastructure\PersonalData\Doctrine;

use Becklyn\Ddd\Events\Domain\DomainEvent;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalData;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordId;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordNotFoundException;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataStore;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-23
 */
class DoctrinePersonalDataStore implements PersonalDataStore
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function storePersonalData(PersonalData $personalData, DomainEvent $parentEvent) : PersonalDataRecordId
    {
        $id = PersonalDataRecordId::next();

        $this->em->persist(
            new DoctrineStoredPersonalDataRecord(
                $id,
                $personalData,
                $parentEvent
            )
        );

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function loadPersonalValue(PersonalDataRecordId $id) : ?string
    {
        /** @var ?DoctrineStoredPersonalDataRecord $record */
        $record = $this->em->getRepository(DoctrineStoredPersonalDataRecord::class)->find($id->asString());

        if (null === $record) {
            throw PersonalDataRecordNotFoundException::forId($id);
        }

        return $record->personalValue();
    }
}
