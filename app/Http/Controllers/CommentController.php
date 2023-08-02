<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentCollection;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\CommentResource;
use App\Http\Requests\StoreCommentRequest;

class CommentController extends Controller
{
    public function __construct()
    {
        Route::bind('task', function ($value) {
            return Task::findOrFail($value);
        });
        Route::bind('projects', function ($value) {
            return Project::findOrFail($value);
        });
    }

    public function index(Request $request, Project|Task $model)
    {
        $comments = $model->comments()->orderByDesc('created_at')->paginate();

        return new CommentCollection($comments);
    }

    public function store(StoreCommentRequest $request, Project|Task $model)
    {
        $validated = $request->validated();

        $comment = $model->comments()->make($validated);

        $comment->user()->associate(Auth::user());

        $comment->save();

        return new CommentResource($comment);
    }
}