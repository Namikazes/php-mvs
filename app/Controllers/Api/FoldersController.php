<?php

namespace App\Controllers\Api;

use App\Models\Folder;
use App\Validators\Folders\CreateFolderValidator;
use Enums\SQL;
use Enums\SQLorder;

class FoldersController extends BaseApiController
{
    public function index()
    {
        return $this->response(
            200,
            Folder::where('user_id', '=', authId())
                ->orWhere('user_id', 'IS', SQL::NULL->value)
                ->orderBy([
                    'user_id' => SQLorder::ASC,
                    'title' => SQLorder::ASC,
                ])
                ->get()
        );
    }
    public function show(int $id)
    {
        $folder = Folder::find($id);

       if($folder &&  !is_null($folder->user_id) && $folder->user_id !== authId()){
           return $this->response(403, [], [
               'message' => 'This resource is forbidden for you'
           ]);
       }
        return $this->response(body: $folder->toArray());
    }
    public function store()
    {
        $data = array_merge(
            requestBody(),
            ['user_id' => authId()]
        );

        $validator = new CreateFolderValidator();
        $validator->validate($data);

        if($validator->validate($data) && $folder = Folder::create($data)) {
            return $this->response(200, [], $folder->toArray());
        }

        return $this->response(200, [], $validator->getErr());
    }
    public function update(int $id)
    {
        $folder = Folder::find($id);

        if ($folder && is_null($folder->user_id) && $folder->user_id !== authId()) {
            return $this->response(403, [
                'message' => 'This resource is forbidden for you'
            ]);
        }

        $data = [
            ...requestBody(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $validator = new CreateFolderValidator();

        if ($validator->validate($data) && $folder = $folder->update($data)) {
            return $this->response($folder->toArray());
        }

        return $this->response(200, [], $validator->getErr());

    }
    public function remove(int $id)
    {
        $folder = Folder::find($id);

        if ($folder && is_null($folder->user_id) && $folder->user_id !== authId()) {
            return $this->response(403, [
                'message' => 'This resource is forbidden for you'
            ]);
        }
        $result = Folder::remove($id);

        if (!$result) {
            return $this->response(403,[], [
                'message' => 'Opps'
            ]);
        }
        return $this->response();
    }
}