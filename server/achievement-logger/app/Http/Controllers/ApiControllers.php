<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function user(Request $request)
    {
        return response()->json($request->user, 200);
    }
}
