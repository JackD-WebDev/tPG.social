<?php
namespace App\Repositories\Eloquent;

use App\Models\Video;
use App\Repositories\Contracts\VideoInterface;

class VideoRepository extends BaseRepository implements VideoInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return Video::class;
    }

    /**
     * @param $videoId
     * @return mixed
     */
    public function incrementViews($videoId): mixed
    {
        $video = $this->model->findOrFail($videoId);

        return $video->increment('views');
    }
}
