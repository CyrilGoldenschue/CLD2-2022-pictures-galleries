<?php

namespace App\Http\Controllers;

use App\Models\Picture;
use App\Models\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PictureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Gallery $gallery)
    {
        $pictures = $gallery->pictures;
        return view('pictures.index', compact('pictures', 'gallery'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Gallery $gallery)
    {
        return view('pictures.create', compact('gallery'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Gallery $gallery, Request $request): RedirectResponse
    {
        $picture = Picture::make($request->all());
        $picture->gallery()->associate($gallery);

        $picture->path = $request->file('picture_file')?->store(
            'cyrsou-galleries/'.$gallery->id,
            's3'
        );

        $picture->save();

        return redirect()->route('galleries.pictures.index', $gallery);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Picture  $picture
     * @return \Illuminate\Http\Response
     */
    public function show(Gallery $gallery, Picture $picture, Request $request)
    {
        if(\Str::startsWith($request->header("Accept"), ["image/"])){
            //rendre le fichier
            //return \Storage::download($picture->path);
            return redirect(\Storage::disk("s3")->temporaryUrl($picture->path, now()->addMinutes(1)));
            //$picture->path = \Storage::disk('s3')->put('galleries/'.$gallery->id, $request->file('picture_file'));

        }else{
            //sinon rendre le html
            return view();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Picture  $picture
     * @return \Illuminate\Http\Response
     */
    public function destroy(Picture $picture)
    {
        //
    }
}
