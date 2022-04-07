<?php declare(strict_types=1);

namespace Becklyn\Ddd\Events\Domain\PersonalData;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-18
 */
interface PersonalData
{
    public const ANONYMIZED_STRING = 'ANONYMIZED';

    public function daysToLive() : int;
    public function personalValue() : mixed;
    public function personalValueAsString() : ?string;
}
