<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Domain;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-23
 */
class PersonalDataRecordNotFoundException extends \Exception
{
    public static function forId(PersonalDataRecordId $id) : self
    {
        return new self("Personal data record with id '{$id->asString()}' could not be found");
    }
}
