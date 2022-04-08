<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Domain;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-22
 */
abstract class AbstractPersonalData implements PersonalData
{
    protected int $daysToLive;
    protected ?string $personalValue;

    protected function __construct(
        ?string $personalValue,
        int $daysToLive,
    ) {
        $this->personalValue = $personalValue;
        $this->daysToLive = $daysToLive;
    }

    public function daysToLive() : int
    {
        return $this->daysToLive;
    }

    public function personalValueAsString() : ?string
    {
        return $this->personalValue;
    }
}
