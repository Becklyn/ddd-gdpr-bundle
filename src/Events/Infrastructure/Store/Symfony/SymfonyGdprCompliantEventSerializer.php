<?php declare(strict_types=1);

namespace Becklyn\Ddd\Events\Infrastructure\Store\Symfony;

use Becklyn\Ddd\Events\Domain\DomainEvent;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordId;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataRecordNotFoundException;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalDataStore;
use Becklyn\Ddd\Events\Domain\PersonalData\PersonalNullableString;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-22
 */
class SymfonyGdprCompliantEventSerializer implements SerializerInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private PersonalDataStore $personalDataStore,
    ) {}

    public function serialize(mixed $data, string $format, array $context = []) : string
    {
        if (!($data instanceof DomainEvent)) {
            return $this->serializer->serialize($data, $format, $context);
        }

        $dataString = $this->serializer->serialize($data, 'json');

        $dataArray = \json_decode($dataString, true);

        $this->serializeSubstitutionLoopOverDataArray($dataArray, $data);

        return \json_encode($dataArray);
    }

    private function serializeSubstitutionLoopOverDataArray(array &$data, DomainEvent $parentEvent) : void
    {
        foreach ($data as &$prop) {
            if (!\is_array($prop)) {
                continue;
            }

            if (\array_key_exists('personalValue', $prop) && \array_key_exists('daysToLive', $prop)) {
                $reference = $this->personalDataStore->storePersonalData(
                    PersonalNullableString::create($prop['personalValue'], $prop['daysToLive']),
                    $parentEvent
                )->asString();

                $prop['personalValue'] = $reference;
            } else {
                $this->serializeSubstitutionLoopOverDataArray($prop, $parentEvent);
            }
        }
    }

    /**
     * @throws PersonalDataRecordNotFoundException
     */
    public function deserialize(mixed $data, string $type, string $format, array $context = []) : mixed
    {
        $dataArray = \json_decode($data, true);

        $this->deserializeSubstitutionLoopOverDataArray($dataArray);

        return $this->serializer->deserialize(\json_encode($dataArray), $type, 'json', $context);
    }

    /**
     * @throws PersonalDataRecordNotFoundException
     */
    private function deserializeSubstitutionLoopOverDataArray(array &$data) : void
    {
        foreach ($data as &$prop) {
            if (!\is_array($prop)) {
                continue;
            }

            if (\array_key_exists('personalValue', $prop) && \array_key_exists('daysToLive', $prop)) {
                $prop['personalValue'] = $this->personalDataStore->loadPersonalValue(PersonalDataRecordId::fromString($prop['personalValue']));
            } else {
                $this->deserializeSubstitutionLoopOverDataArray($prop);
            }
        }
    }
}
