<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Server;

class ServerController extends Controller
{
    function create($id_episode, Request $rq)
    {
        if (!$id_episode) {
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng load lại trang']);
        }

        try {
            Server::create(['images' => $rq->images, 'id_episode' => $id_episode]);
            return response()->json(['success' => 'Thêm server  thành công']);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);

        }
    }
}
