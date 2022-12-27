<?php
namespace App\Repositories\Eloquent;

use App\Models\Comment;
use App\Repositories\Contracts\CommentInterface;

class CommentRepository extends BaseRepository implements CommentInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Comment::class;
    }
}
