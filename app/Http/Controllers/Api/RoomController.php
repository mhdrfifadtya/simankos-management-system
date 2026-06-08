<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function show(Request $request)
    {
        $tenant = $request->user()->tenant;
        if (!$tenant) {
            return response()->json(['message' => 'Data penyewa tidak ditemukan.'], 404);
        }

        $rental = $tenant->activeRental;
        if (!$rental) {
            return response()->json(['message' => 'Tidak ada kontrak aktif.'], 404);
        }

        $room = $rental->room->load(['photos', 'facilities']);
        return new RoomResource($room);
    }
}
