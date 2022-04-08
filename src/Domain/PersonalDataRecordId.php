<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Domain;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-03-23
 */
final class PersonalDataRecordId
{
    private function __construct(
        private string $id,
    ) {
        Assert::uuid($id);
    }

    public static function next() : self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id) : self
    {
        return new self($id);
    }

    public function asString() : string
    {
        return $this->id;
    }
}
