<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\Postgres\Transaction;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Transaction\OptimisticLockTest as CommonTest;

/**
 * @group driver
 * @group driver-postgres
 */
class OptimisticLockTest extends CommonTest
{
    public const DRIVER = 'postgres';
}