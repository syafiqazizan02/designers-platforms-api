<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\IDesign;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Eloquent\Criteria\{
    LatestFirst,
    IsLive,
    // ForUser
};

class DesignController extends Controller
{
    protected $designs;

    public function __construct(IDesign $designs) // import design contract
    {
        $this->designs = $designs;
    }

    public function index() // get all from design database return to resource collection
    {
        // group by cateria function
        $designs = $this->designs->withCriteria([
            new LatestFirst(),
            new IsLive(),
            // new ForUser()
        ])->all();

        return DesignResource::collection($designs);
    }

    public function findDesign($id) // get designs by id
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request, $id)
    {
        // apply method find($id) from BaseRepository
        $design = $this->designs->find($id); // update by current user

        $this->authorize('update', $design); // make auth first with design policy @ update

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,'. $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
             'tags' => ['required']
        ]);

        // update the images
        $design = $this->designs->update($id, [  // apply method ($id, array $data) from BaseRepository
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title), //slug auto generate
            'is_live' => ! $design->upload_successful ? false : $request->is_live // is publish or not
        ]);

        // apply the tags
        $this->designs->applyTags($id, $request->tags);

        return new DesignResource($design); // retturn custom @ selected response (attribute)
    }

    public function destroy($id)
    {
        // apply method find($id) from BaseRepository
        $design = $this->designs->find($id); // delete by current user

        $this->authorize('delete', $design); // make auth first with design policy @ delete

        // delete the files associated to the record
        foreach(['thumbnail', 'large', 'original'] as $size){
            // check if the file exists in the database
            if(Storage::disk($design->disk)->exists("uploads/designs/{$size}/".$design->image)){
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/".$design->image);
            }
        }

        // must delete in local disk then delete db
        $this->designs->delete($id);

        return response()->json(['message' => 'Record deleted'], 200);
    }
}
