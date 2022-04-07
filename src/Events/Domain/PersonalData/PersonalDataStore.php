<?php declare(strict_types=1);

namespace Becklyn\Ddd\Events\Domain\PersonalData;

use Becklyn\Ddd\Events\Domain\DomainEvent;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-23
 */
interface PersonalDataStore
{
    public function storePersonalData(PersonalData $personalData, DomainEvent $parentEvent) : PersonalDataRecordId;

    /**
     * @throws PersonalDataRecordNotFoundException
     */
    public function loadPersonalValue(PersonalDataRecordId $id) : ?string;
}
