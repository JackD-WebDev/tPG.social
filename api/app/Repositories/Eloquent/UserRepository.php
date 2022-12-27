<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserInterface;
use MatanYadaev\EloquentSpatial\Objects\Point;


class UserRepository extends BaseRepository implements UserInterface
{
    /**
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function findByEmail($email): mixed
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request): mixed
    {
        $query = (new $this->model)->newQuery();
        if ($request->has_projects) {
            $query->has('projects');
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;

        if ($lat && $lng) {
            $point = new Point($lat, $lng);
            $unit == 'km' ? $dist *= 1000 : $dist *= 1609.34;
            $query->distanceSphereExcludingSelf('location', $point, $dist);
        }

        if ($request->orderByLatest) {
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->get();
    }
}
