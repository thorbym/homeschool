<?php

namespace App\Http\Controllers;

use Auth;
use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = null;

        $view = view('modals.categoryCrud', compact('category'))->render();

        return response()->json($view);
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
            'category' => 'required',
            'colour' => 'required',
            'font_colour' => 'required',
        ]);

        $category = new Category([
            'category' => $request->get('category'),
            'colour' => $request->get('colour'),
            'font_colour' => $request->get('font_colour')
        ]);

        $category->save();

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check() && Auth::user()->isAdmin()) {

            $category = Category::where('id', $id)->first();

            $view = view('modals.categoryCrud', compact('category'))->render();

            return response()->json($view);
        }
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
        $request->validate([
            'category' => 'required',
            'colour' => 'required',
            'font_colour' => 'required',
        ]);

        $category = Category::find($id);

        $category->category = $request->get('category');
        $category->colour = $request->get('colour');
        $category->font_colour = $request->get('font_colour');

        $category->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::destroy($id);

        return redirect()->back();
    }
}
