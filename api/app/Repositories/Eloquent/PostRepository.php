<?php
namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostInterface;

class PostRepository extends BaseRepository implements PostInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Post::class;
    }

}
