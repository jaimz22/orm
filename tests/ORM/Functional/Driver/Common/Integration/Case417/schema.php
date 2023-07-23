<?php

declare(strict_types=1);

use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\Relation;
use Cycle\ORM\SchemaInterface as Schema;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Select\Source;
use Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417\Entity\Comment;
use Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417\Entity\Post;
use Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417\Entity\PostTag;
use Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417\Entity\PostType;
use Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417\Entity\Tag;
use Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417\Entity\User;

return [
    'post' => [
        Schema::ENTITY => Post::class,
        Schema::SOURCE => Source::class,
        Schema::DATABASE => 'default',
        Schema::MAPPER => Mapper::class,
        Schema::TABLE => 'post',
        Schema::PRIMARY_KEY => ['id'],
        Schema::FIND_BY_KEYS => ['id'],
        Schema::COLUMNS => [
            'id' => 'id',
            'slug' => 'slug',
            'title' => 'title',
            'public' => 'public',
            'content' => 'content',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'published_at' => 'published_at',
            'deleted_at' => 'deleted_at',
            'user_id' => 'user_id',
        ],
        Schema::RELATIONS => [
            'postType'=>[
                Relation::TYPE => Relation::BELONGS_TO,
                Relation::TARGET => PostType::ROLE,
                Relation::LOAD => Relation::LOAD_PROMISE,
                Relation::SCHEMA => [
                    Relation::CASCADE => true,
                    Relation::NULLABLE => false,
                    Relation::INNER_KEY => 'post_type_id',
                    Relation::OUTER_KEY => ['id'],
                ],
            ]
        ],
        Schema::TYPECAST => [
            'id' => 'int',
            'public' => 'bool',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'published_at' => 'datetime',
            'deleted_at' => 'datetime',
            'user_id' => 'int',
        ],
        Schema::SCHEMA => [],
    ],
    'post_type'=> [
        Schema::ENTITY => PostType::class,
        Schema::SOURCE => Source::class,
        Schema::DATABASE => 'default',
        Schema::MAPPER => Mapper::class,
        Schema::TABLE => 'post_type',
        Schema::PRIMARY_KEY => ['id'],
        Schema::FIND_BY_KEYS => ['id'],
        Schema::COLUMNS => [
            'id' => 'id',
            'name' => 'name',
        ],
        Schema::RELATIONS => [],
        Schema::TYPECAST => [
            'id' => 'int',
        ],
        Schema::SCHEMA => [],
    ]
];
