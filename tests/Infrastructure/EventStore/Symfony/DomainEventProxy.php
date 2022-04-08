<?php declare(strict_types=1);

namespace Becklyn\Ddd\PersonalData\Tests\Infrastructure\EventStore\Symfony;

use Becklyn\Ddd\Events\Domain\AbstractDomainEvent;
use Becklyn\Ddd\Identity\Domain\AggregateId;

/**
 * @author Marko Vujnovic <mv@becklyn.com>
 *
 * @since  2022-04-07
 */
class DomainEventProxy extends AbstractDomainEvent
{
    public function aggregateId() : AggregateId
    {
        return AggregateIdProxy::next();
    }

    public function aggregateType() : string
    {
        return 'foo';
    }
}
