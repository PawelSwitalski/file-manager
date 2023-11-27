<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles()
    {
        return Inertia::render('MyFiles');
    }

    public function createFolder(StoreFolderRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        if (!$parent) {
            $parent = $this->createRootFolder();
        }

        $folder = new File();
        $folder->name = $data['name'];
        $folder->is_folder = true;

        $parent->appendNode($folder);

//        return Inertia::render('CreateFile');
    }

    private function createRootFolder()
    {
        return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
    }
}
