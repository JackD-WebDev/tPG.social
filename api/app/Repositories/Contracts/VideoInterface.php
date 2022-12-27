<?php

namespace App\Repositories\Contracts;

interface VideoInterface extends BaseInterface
{
    public function incrementViews($videoId);

    public function addComment($videoId, array $array);
}
