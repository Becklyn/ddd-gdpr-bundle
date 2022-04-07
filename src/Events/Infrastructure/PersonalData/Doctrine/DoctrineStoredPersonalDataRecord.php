<?php declare(strict_types=1);

namespace Becklyn\Ddd\Events\Infrastructure\PersonalData\Doctrine;

use Becklyn\Ddd\Events\Domain\DomainEvent;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalData;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-23
 */
#[ORM\Entity]
#[ORM\Table(name:"personal_data_store")]
class DoctrineStoredPersonalDataRecord
{
    #[Orm\Id]
    #[Orm\Column(name: "id", type: "string", length: 36)]
    #[Orm\GeneratedValue(strategy: "NONE")]
    private string $id;

    #[Orm\Column(name: "aggregate_id", type: "string", length: 36, nullable: false)]
    private string $aggregateId;

    #[Orm\Column(name: "personal_value", type: "string", nullable: true)]
    private string $personalValue;

    #[Orm\Column(name: "days_to_live", type: "int", nullable: false)]
    private int $daysToLive;

    #[Orm\Column(name: "event_raised_ts", type: "datetime_immutable", nullable: false)]
    private \DateTimeImmutable $eventRaisedTs;

    #[Orm\Column(name: "expires_ts", type: "datetime_immutable", nullable: false)]
    private \DateTimeImmutable $expiresTs;

    #[Orm\Column(name: "anonymized_ts", type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $anonymizedTs = null;

    #[Orm\Column(name: "created_ts", type: "datetime_immutable", nullable: false)]
    private \DateTimeImmutable $createdTs;

    public function __construct(PersonalDataRecordId $id, PersonalData $personalData, DomainEvent $parentEvent)
    {
        $this->id = $id->asString();
        $this->aggregateId = $parentEvent->aggregateId()->asString();
        $this->personalValue = $personalData->personalValueAsString();
        $this->daysToLive = $personalData->daysToLive();
        $this->eventRaisedTs = $parentEvent->raisedTs();
        $this->expiresTs = $this->eventRaisedTs->add(\DateInterval::createFromDateString("{$this->daysToLive} days"));
        $this->createdTs = new \DateTimeImmutable();
    }

    public function id() : PersonalDataRecordId
    {
        return PersonalDataRecordId::fromString($this->id);
    }

    public function aggregateId() : string
    {
        return $this->aggregateId;
    }

    public function personalValue() : ?string
    {
        if (!$this->isAnonymized() && $this->expiresTs <= new \DateTimeImmutable()) {
            $this->anonymize();
        }

        return $this->personalValue;
    }

    public function daysToLive() : int
    {
        return $this->daysToLive;
    }

    public function eventRaisedTs() : \DateTimeImmutable
    {
        return $this->eventRaisedTs;
    }

    public function expiresTs() : \DateTimeImmutable
    {
        return $this->expiresTs;
    }

    public function anonymizedTs() : ?\DateTimeImmutable
    {
        return $this->anonymizedTs;
    }

    public function anonymize() : void
    {
        if ($this->isAnonymized()) {
            return;
        }

        $this->personalValue = PersonalData::ANONYMIZED_STRING;
        $this->anonymizedTs = new \DateTimeImmutable();
    }

    public function isAnonymized() : bool
    {
        return PersonalData::ANONYMIZED_STRING === $this->personalValue;
    }
}
