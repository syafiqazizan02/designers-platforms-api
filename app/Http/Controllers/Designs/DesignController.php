<?php

namespace App\Http\Controllers\Designs;

use App\Models\Design;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;

class DesignController extends Controller
{
    public function update(Request $request, $id)
    {
        $design = Design::findOrFail($id); // update by current user

        $this->authorize('update', $design); // make auth first with design policy @ update

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,'. $id],
            'description' => ['required', 'string', 'min:20', 'max:140']
        ]);

        // update the images
        $design->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title), //slug auto generate
            'is_live' => ! $design->upload_successful ? false : $request->is_live // is publish or not
        ]);

        return new DesignResource($design); // retturn custom @ selected response (attribute)
    }
}
