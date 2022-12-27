<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;

interface ProjectInterface extends BaseInterface
{
    public function addComment($projectId, array $data);
    public function search(Request $request);
}
