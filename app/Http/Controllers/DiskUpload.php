<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Jobs\FileUpload;

class DiskUpload extends Controller
{
    public function upload(Request $request)
    {
        if (!$request->accepts(['multipart/form-data'])) {
            return redirect('/upload');
        }

        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
        $save = $receiver->receive();

        if ($save->isFinished() === false) {
            $handler = $save->handler();
            return response()->json([
                "done" => $handler->getPercentageDone(),
            ]);
        }

        $file = $save->getFile();

        $fileName = $file->getClientOriginalName();
        $filePath = $file->path();
        $folderPath = $request->query('folderPath');

        FileUpload::dispatch($fileName, $filePath, $folderPath);

        return redirect('/upload');
    }

    public function show()
    {
        return view('upload');
    }
}
