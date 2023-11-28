<?php

namespace App\Http\Controllers\Api\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PingController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'pong',
            'data' => [
                'request' => $request->all(),
            ],
        ]);
    }
}
