<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface UserInterface extends BaseInterface
{
    /**
     * @param $email
     * @return mixed
     */
    public function findByEmail($email): mixed;

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request): mixed;
}
