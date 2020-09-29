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
    EagerLoad
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
            new EagerLoad(['user', 'comments'])
        ])->all();

        return DesignResource::collection($designs);
    }

    public function findBySlug($slug)
    {
        $design = $this->designs
                        ->withCriteria([new IsLive()])
                        ->findWhereFirst('slug', $slug);

        return new DesignResource($design);
    }

    public function findDesign($id) // get designs by id
    {
        $design = $this->designs->find($id);
        
        return new DesignResource($design);
    }

    public function getForTeam($teamId) //get a design for a team
    {
        $designs = $this->designs
                        ->withCriteria([new IsLive()])
                        ->findWhere('team_id', $teamId);

        return DesignResource::collection($designs);
    }

    public function getForUser($userId)
    {
        $designs = $this->designs
                        ->withCriteria([new IsLive()])
                        ->findWhere('user_id', $userId);
        return DesignResource::collection($designs);
    }

    public function update(Request $request, $id)
    {
        // apply method find($id) from BaseRepository
        $design = $this->designs->find($id); // update by current user

        $this->authorize('update', $design); // make auth first with design policy @ update

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,'. $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
             'tags' => ['required'],
             'team' => ['required_if:assign_to_team,true']
        ]);

        // update the images
        $design = $this->designs->update($id, [  // apply method ($id, array $data) from BaseRepository
            'team_id' => $request->team,
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

    public function search(Request $request)
    {
        $designs = $this->designs->search($request);

        return DesignResource::collection($designs);
    }

    // for a single likes
    public function like($id)
    {
        $this->designs->like($id);
        return response()->json(['message' => 'Successful'], 200);
    }

    // check if user already likes
    public function checkIfUserHasLiked($designId)
    {
        $isLiked = $this->designs->isLikedByUser($designId);
        return response()->json(['liked' => $isLiked], 200);
    }

}
