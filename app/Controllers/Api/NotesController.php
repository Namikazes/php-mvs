<?php

namespace App\Controllers\Api;

use App\Models\Notes;
use App\Validators\Notes\CreateNotesValidator;
use App\Validators\Notes\UpdateNotesValidator;
use Enums\SQLorder;

class NotesController extends BaseApiController
{
    public function index()
    {
        return $this->response(
            body: Notes::where('user_id', '=', authId())
                ->orderBy([
                    'updated_at' => SQLorder::DESC,
                ])
                ->get()
        );
    }
    public function show(int $id)
    {
        $note = Notes::find($id);

       if($note && $note->user_id !== authId()){
           return $this->response(403, [], [
               'message' => 'This resource is forbidden for you'
           ]);
       }
        return $this->response(body: $note->toArray());
    }
    public function store()
    {
        $data = array_merge(
            requestBody(),
            ['user_id' => authId()]
        );

        $validator = new CreateNotesValidator();

        if($validator->validate($data) && $note = Notes::create($data)) {
            return $this->response(200, [], $note->toArray());
        }

        return $this->response(200, [], $validator->getErr());
    }
    public function update(int $id)
    {
        $note = Notes::find($id);

        if ($note && $note->user_id !== authId()) {
            return $this->response(403, [
                'message' => 'This resource is forbidden for you'
            ]);
        }

        $data = [
            ...requestBody(),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $validator = new UpdateNotesValidator($note);

        if ($validator->validate($data) && $note = $note->update($data)) {
            return $this->response(body: $note->toArray());
        }

        return $this->response(200, [], $validator->getErr());

    }
    public function remove(int $id)
    {
        $note = Notes::find($id);

        if ($note && $note->user_id !== authId()) {
            return $this->response(403, [
                'message' => 'This resource is forbidden for you'
            ]);
        }
        $result = Notes::remove($id);

        if (!$result) {
            return $this->response(403,[], [
                'message' => 'Opps'
            ]);
        }
        return $this->response();
   }
}