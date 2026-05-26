<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class DiskIndex extends Controller
{
    public function index(Request $request)
    {
        $token = env('YANDEX_TOKEN');
        $disk = new \Arhitector\Yandex\Disk($token);
        
        $page = max(1, $request->integer('page', 1));

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $collection = $disk->getResources($limit + 1, $offset);
        $collection->setSort('name');
        $collection->setPreview('M');

        $paginated = new Paginator(
            $collection,
            $limit,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view('index', ['items' => $paginated]);
    }

    public function delete(Request $request)
    {
        $token = env('YANDEX_TOKEN');
        $disk = new \Arhitector\Yandex\Disk($token);

        $path = $request->path;

        $resource = $disk->getResource($path);
        if ($resource->has()) {
            $resource->delete();
        }

        return redirect('/');
    }
}
