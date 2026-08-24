<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function getAllRecord(Request $request)
    {
        $data = $request->validate(['provinceId' => ['required', 'exists:provinces,id']]);
        $districts = Province::findOrFail($data['provinceId'])->districts;

        return \Response::json($districts);
    }
}
