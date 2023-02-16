<?php

namespace Chaos\Majordomo\Http\Controllers;

use \Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function response($result)
    {
        return response(['result' => $result, "errorCode" => 0]);
    }

    public function pageReponse($paginator)
    {
        return $this->response([
            'total' => $paginator->total(),
            'data' => $paginator->items()
        ]);
    }
}
