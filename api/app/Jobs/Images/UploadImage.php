<?php

namespace App\Jobs\Images;

use Log;
use File;
use Image;
use Exception;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Project $project;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $disk = $this->project->disk;
        $filename = $this->project->image;
        $original_file = storage_path('uploads/original/'.$filename);


        try{
            Image::make($original_file)
                ->fit(800, 600, function($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($large = storage_path('uploads/large/'.$filename));

            Image::make($original_file)
                ->fit(250, 200, function($constraint) {
                    $constraint->aspectRatio();
                })
                ->save($thumbnail = storage_path('uploads/thumbnail/'.$filename));

            if(Storage::disk($disk)
                ->put('uploads/projects/original/'.$filename, fopen($original_file, 'r+'))) {
                    File::delete($original_file);
                }

            if(Storage::disk($disk)
                ->put('uploads/projects/large/'.$filename, fopen($large, 'r+'))) {
                    File::delete($large);
                }

            if(Storage::disk($disk)
                ->put('uploads/projects/thumbnail/'.$filename, fopen($thumbnail, 'r+'))) {
                    File::delete($thumbnail);
                }

                $this->project->update([
                    'upload_successful' => true
                ]);


        } catch(Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
