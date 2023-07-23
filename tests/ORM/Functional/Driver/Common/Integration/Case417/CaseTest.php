<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417;

use Cycle\ORM\Select;
use Cycle\ORM\Tests\Functional\Driver\Common\BaseTest;
use Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417\Entity\Post;
use Cycle\ORM\Tests\Functional\Driver\Common\Integration\IntegrationTestTrait;
use Cycle\ORM\Tests\Traits\TableTrait;

abstract class CaseTest extends BaseTest
{
    use IntegrationTestTrait;
    use TableTrait;

    public function setUp(): void
    {
        // Init DB
        parent::setUp();
        $this->makeTables();
        $this->fillData();

        $this->loadSchema(__DIR__ . '/schema.php');
    }

//    public function testSelect(): void
//    {
//        // Get entity
//        $user = (new Select($this->orm, Entity\User::class))
//            ->load('posts')
//            ->wherePK(2)
//            ->fetchOne();
//
//        // Check result
//        $this->assertInstanceOf(Entity\User::class, $user);
//        $this->assertCount(2, $user->posts);
//    }

    public function testSave(): void
    {
        // Get entity
        $postType = (new Select($this->orm, Entity\PostType::class))
            ->wherePK(1)
            ->fetchOne();

        $post = new Post('title', 'content');
        $post->postType = $postType;

        // Store changes and calc write queries
        $this->captureWriteQueries();
        $this->save($post);

        // Check write queries count
        $this->assertNumWrites(1);
    }

    private function makeTables(): void
    {
        // Make tables
        $this->makeTable(Entity\PostType::ROLE, [
            'id' => 'primary', // autoincrement
            'name' => 'string',
        ]);

        $this->makeTable('post', [
            'id' => 'primary',
            'slug' => 'string',
            'title' => 'string',
            'public' => 'bool',
            'content' => 'string',
            'type_id' => 'int',
            'published_at' => 'datetime,nullable',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime,nullable',
        ]);
        $this->makeFK('post', 'type_id', 'post_type', 'id', 'NO ACTION', 'NO ACTION');
    }

    private function fillData(): void
    {
        $this->getDatabase()->table('post_type')->insertMultiple(
            ['id', 'name'],
            [
                ['1', 'Home'],
                ['2', 'Work'],
                ['3', 'Office'],
                ['4', 'Other'],
            ],
        );
    }
}
