<?php

namespace App\Repositories\Contracts;

interface PostInterface extends BaseInterface
{
    public function applyTags($id, array $data);
    public function addComment($postId, array $data);
    public function support($id);
    public function isSupportedByUser($id);
}
