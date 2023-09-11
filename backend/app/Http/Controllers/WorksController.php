<?php

namespace App\Http\Controllers;

use App\Models\Works;
use Illuminate\Http\Request;

class WorksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        return Works::all();
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
            'title' => 'required|min:2|unique:App\Models\Works,title',
            'tag' => 'required'
        ]);

        return Works::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = Works::find($id);
        if (empty($result)) {
            return response(['not_found' => true]);
        }
        return Works::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $work = Works::find($id);
        if (empty($work))
            return response(['no_work' => true]);

        $request->validate([
            'title' => 'min:2|unique:App\Models\Works,title'
        ]);

        $work->update($request->all());
        return $work;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $work = Works::find($id);
        if (empty($work))
            return response(['no_work' => true]);

        return $work->delete();
    }
}