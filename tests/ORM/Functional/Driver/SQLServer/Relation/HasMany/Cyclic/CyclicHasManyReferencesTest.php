<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\SQLServer\Relation\HasMany\Cyclic;

// phpcs:ignore
use Cycle\ORM\Tests\Functional\Driver\Common\Relation\HasMany\Cyclic\CyclicHasManyReferencesTest as CommonTest;

/**
 * @group driver
 * @group driver-sqlserver
 */
class CyclicHasManyReferencesTest extends CommonTest
{
    public const DRIVER = 'sqlserver';
}