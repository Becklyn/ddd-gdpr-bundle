<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Domain;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-22
 */
class PersonalNullableString extends AbstractPersonalData
{
    public static function create(?string $personalValue, int $daysToLive = 30) : self
    {
        return new self($personalValue, $daysToLive);
    }

    public function personalValue() : ?string
    {
        return $this->personalValue;
    }
}
