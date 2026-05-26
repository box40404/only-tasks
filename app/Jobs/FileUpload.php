<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Arhitector\Yandex\Disk;

class FileUpload implements ShouldQueue
{
    use Queueable;

    public $timeout = 0;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $fileName,
        public string $filePath,
        public ?string $uploadFolder
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = env('YANDEX_TOKEN');
        $disk = new Disk($token);

        if (isset($this->uploadFolder)) {
            $uploadFolder = $disk->getResource($this->uploadFolder);
            if (!($uploadFolder->has() && $uploadFolder->isDir())) {
                $uploadFolder->create();
            }

            $uploadFile = $disk->getResource($uploadFolder->path.'/'.$this->fileName);
        } else {
            $uploadFile = $disk->getResource($this->fileName);
        }

        if ($uploadFile->has()) {
            $nameParts = explode('.', $this->fileName, 2);
            $fileStem = $nameParts[0] . '-' . str()->random(5);
            $fileExt = $nameParts[1];

            $newFileName = $fileStem . '.' . $fileExt;
            if (isset($uploadFolder)) {
                $uploadFile = $disk->getResource($uploadFolder->path.'/'.$newFileName);
            } else {
                $uploadFile = $disk->getResource($newFileName);
            }            
        }

        $uploadFile->upload($this->filePath);

        unlink($this->filePath);
    }
}
