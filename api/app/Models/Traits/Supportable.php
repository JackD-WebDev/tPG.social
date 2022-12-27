<?php
namespace App\Models\Traits;

use App\Models\Support;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Supportable
{
    /**
     * @return void
     */
    public static function bootSupportable(): void
    {
        static::deleting(function($model) {
            $model->removeSupports();
        });
    }

    /**
     * @return void
     */
    public function removeSupports(): void
    {
        if($this->supports()->count()) {
            $this->supports()->delete();
        }
    }

    /**
     * @return MorphMany
     */
    public function supports(): MorphMany
    {
        return $this->morphMany(Support::class, 'supportable');
    }

    /**
     * @return void
     */
    public function support(): void
    {
        if(!auth()->check()) {
            return;
        }

        if($this->isSupportedByUser(auth()->id())) {
            return;
        }

        $this->supports()->create(['user_id' => auth()->id()]);
    }

    /**
     * @return void
     */
    public function unSupport(): void
    {
        if(! auth()->check()) return;
        if(! $this->isSupportedByUser(auth()->id())) {
            return;
        }

        $this->supports()->where(
            'user_id',
            auth()->id()
        )->delete();
    }

    /**
     * @param $user_id
     * @return bool
     */
    public function isSupportedByUser($user_id): bool
    {
        return (bool)$this->supports()
            ->where('user_id', $user_id)
            ->count();
    }
}
