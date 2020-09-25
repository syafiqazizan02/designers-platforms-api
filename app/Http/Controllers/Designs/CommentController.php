<?php

namespace App\Http\Controllers\Designs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Contracts\IComment;

class CommentController extends Controller
{
    protected $comments;
    protected $designs;

    public function __construct(IComment $comments, IDesign $designs)
    {
        $this->comments = $comments;
        $this->designs = $designs;
    }

    public function store(Request $request, $designId) // apply morphMany to IDesign
    {
        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->designs->addComment($designId, [ // create on designs repository addComment() w/ design_id
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return new CommentResource($comment);
    }

    public function update(Request $request, $id)
    {
        $comment = $this->comments->find($id);
        $this->authorize('update', $comment); // refer update() on commnet policy

        $this->validate($request, [
            'body' => ['required']
        ]);

        // refer update() IBase
        $comment = $this->comments->update($id, [
            'body' => $request->body
        ]);

        return new CommentResource($comment);
    }

    public function destroy($id)
    {
        $comment = $this->comments->find($id);
        $this->authorize('delete', $comment); // refer delete() on commnet policy

        // refer delete() IBase
        $this->comments->delete($id);
        
        return response()->json(['message' => 'Item deleted'], 200);
    }
}
