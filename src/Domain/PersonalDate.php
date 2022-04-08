<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Domain;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-22
 */
class PersonalDate extends AbstractPersonalData
{
    public static function create(\DateTimeImmutable $personalValue, int $daysToLive = 30) : self
    {
        return new self($personalValue->format('Y-m-d H:i:s.u'), $daysToLive);
    }

    public function personalValue() : \DateTimeImmutable
    {
        if (self::ANONYMIZED_STRING === $this->personalValue) {
            return new \DateTimeImmutable('0001-01-01 00:00:00.000000');
        }

        return new \DateTimeImmutable($this->personalValue);
    }
}
