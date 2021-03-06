<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Tests\Domain;

use Becklyn\Ddd\PersonalData\Domain\AbstractPersonalData;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-04-07
 */
class IntegerAbstractPersonalDataTestProxy extends AbstractPersonalData
{
    public static function create(?int $personalValue, int $daysToLive = 30)
    {
        return new self(null === $personalValue ? null : (string) $personalValue, $daysToLive);
    }

    public function personalValue() : ?int
    {
        return null === $this->personalValue ? null : (int) $this->personalValue;
    }
}
