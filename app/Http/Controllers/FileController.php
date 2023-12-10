<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyFilesRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles(Request $request, string $folder = null)
    {
        if ($folder) {
            $folder = File::query()
                ->where('created_by', Auth::id())
                ->where('path', $folder)
                ->firstOrFail();
        } else {
            $folder = $this->getRoot();
        }

        $files = File::query()
            ->where('created_by', Auth::id())
            ->where('parent_id', $folder->id)
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $files = FileResource::collection($files);

        if ($request->wantsJson()) {
            return $files;
        }

        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

        return Inertia::render('MyFiles', compact('files', 'folder', 'ancestors'));
    }

    public function createFolder(StoreFolderRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        if (!$parent) {
            $parent = $this->getRoot();
        }

        $folder = new File();
        $folder->name = $data['name'];
        $folder->is_folder = true;

        $parent->appendNode($folder);

//        return Inertia::render('CreateFile');
    }

    public function store(StoreFileRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;
        $user = $request->user();
        $fileTree = $request->file_tree;

        if (!$parent) {
            $parent = $this->getRoot();
        }

        if (!empty($fileTree)) {
            $this->storeFileTree($fileTree, $parent, $user);
        } else {
            foreach ($data['files'] as $file) {

                $this->saveFile($file, $user, $parent);
            }
        }
    }

    private function getRoot()
    {
        return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
    }

    public function storeFileTree($fileTree, $parent, $user)
    {
        foreach ($fileTree as $name => $file) {
            if (is_array($file)) {
                $folder = new File();
                $folder->name = $name;
                $folder->is_folder = true;

                $parent->appendNode($folder);
                $this->storeFileTree($file, $folder, $user);
            } else {
                $this->saveFile($file, $user, $parent);
            }
        }
    }

    public function destroy(DestroyFilesRequest $request)
    {
        $data = $request->validated();
        $parent = $request->parent;

        if ($data['all']) {
            $children = $parent->children()->get();

            foreach ($children as $child) {
                $child->delete();
            }
        } else {
            foreach ($data['ids'] as $id) {
                $file = File::find($id);
                $file->delete();
            }
        }


        return to_route('files.index', ['folder' => $parent->path]);
    }

    /**
     * @param $file
     * @param $user
     * @param $parent
     * @return void
     */
    public function saveFile($file, $user, $parent): void
    {
        /** @var \Illuminate\Http\UploadedFile $file */
        $path = $file->store('/files/' . $user->id);
        $model = new File();
        $model->storage_path = $path;
        $model->is_folder = false;
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();
        $parent->appendNode($model);
    }
}
