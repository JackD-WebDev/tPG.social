<?php

namespace App\Jobs\Videos;

use FFMpeg;
use App\Models\Video;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ConvertForStreaming implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Video
     */
    public Video $video;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $min = (new X264('aac'))->setKiloBitrate(100);
        $low = (new X264('aac'))->setKiloBitrate(250);
        $mid = (new X264('aac'))->setKiloBitrate(500);
        $high = (new X264('aac'))->setKiloBitrate(1000);
        $ultra = (new X264('aac'))->setKiloBitrate(1500);

        FFMpeg::fromDisk('stage')
            ->open($this->video->path)
            ->exportForHLS()
            ->onProgress(function($percentage) {
                $this->video->update([
                    'percentage' => $percentage
                ]);
            })
            ->addFormat($min)
            ->addFormat($low)
            ->addFormat($mid)
            ->addFormat($high)
            ->addFormat($ultra)
            ->save("videos/{$this->video->id}/{$this->video->id}.m3u8");
    }
}
