<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CommentRequest;
use App\Http\Resources\CommentResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
class CommentController extends Controller
{
    public function store(CommentRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->user()->id;
        
        $comment = Comment::create($data);
        
        return response([ 'comment' => new 
        CommentResource($comment), 
        'message' => 'Success'], 200);
    }
}
