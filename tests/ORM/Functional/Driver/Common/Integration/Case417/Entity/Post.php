<?php

declare(strict_types=1);

namespace Cycle\ORM\Tests\Functional\Driver\Common\Integration\Case417\Entity;

use DateTimeImmutable;

class Post
{
    public ?int $id = null;
    public string $slug;
    public string $title = '';
    public bool $public = false;
    public string $content = '';
    public PostType $postType;
    public ?int $post_type_id = null;
    public DateTimeImmutable $created_at;
    public DateTimeImmutable $updated_at;
    public ?DateTimeImmutable $published_at = null;
    public ?DateTimeImmutable $deleted_at = null;

    public function __construct(string $title = '', string $content = '')
    {
        $this->title = $title;
        $this->content = $content;
        $this->created_at = new DateTimeImmutable();
        $this->updated_at = new DateTimeImmutable();
        $this->resetSlug();
    }

    public function resetSlug(): void
    {
        $this->slug = \bin2hex(\random_bytes(32));
    }
}