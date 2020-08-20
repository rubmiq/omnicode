<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Tag;
use Illuminate\Http\Request;

class VideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videos = Video::latest()->paginate(20);
        return view('videos.index', compact('videos'))->with('1', (request()->input('page', 1) - 1) * 20);
    }


    public function search()
    {
        /**
         * @TODO - to be implemented
         *
         */
        $query = Video::query();

        if (request()->filled("name_starting")) {
            $query->where('name', 'like',  request()->input('name_starting') . '%')->get();
        }

        if (request()->filled("name_ending")) {
            $query->orWhere('name', 'like', '%' . request()->input('name_ending'));
        }

        $videos = $query->get();
        return view('videos.index')->with('videos', $videos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'description' => 'required',
            'tags' => 'not_regex:[^A-Za-z0-9\/-\/,]'
        ]);

        $video_id = Video::create(['name' => $request->input('name'), 'description' => $request->input('description')])->id;
        $this->insert_tags($request->input('tags'), $video_id);
        return redirect()->route('videos.index')->with('success', "Video created");
    }

    private function insert_tags($tags_string, $video_id)
    {
        $tags_array = explode(',', $tags_string);
        $insert_data = array();
        foreach ($tags_array as $key=>$tag) {
            if (strlen($tag) > 0) {
                $insert_data[$key]['name'] = $tag;
                $insert_data[$key]['video_id'] = $video_id;
            }
        }
        Tag::insert($insert_data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function edit(Video $video)
    {
        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'name' => 'required|max:100',
            'description' => 'required',
            'tags' => 'not_regex:[^A-Za-z0-9\/-\/,]'
        ]);

        $video->name = $request->input('name');
        $video->description = $request->input('description');
        $this->update_tags($request->tags, $video->id);
        $video->save();



        return redirect()->route('videos.index')->with('success', "Video updated");
    }

    private function update_tags($tags_string, $video_id)
    {

        $tags_array = explode(',', $tags_string);
        $insert_data = array();
        Tag::where('video_id', $video_id)->delete();
        foreach ($tags_array as $key=>$tag) {
            if (strlen($tag) > 0) {
                $insert_data[$key]['name'] = $tag;
                $insert_data[$key]['video_id'] = $video_id;
            }
        }
        Tag::insert($insert_data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $video)
    {
        $video->delete();
        return redirect()->route('videos.index')->with('success', "Video deleted");
    }
}
