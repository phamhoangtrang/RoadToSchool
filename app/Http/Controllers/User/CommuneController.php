<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    public function getAllRecord(Request $request)
    {
        $data = $request->validate(['districtId' => ['required', 'exists:districts,id']]);
        $communes = District::findOrFail($data['districtId'])->communes;

        return \Response::json($communes);
    }
}
