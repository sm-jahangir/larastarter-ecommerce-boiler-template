<?php

namespace App\Http\Controllers\Backend;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $categories = Category::where('parent_id', null)->orderBy('order', 'DESC')->get();
        $categories = Category::orderBy('order', 'DESC')->get();
        return view('backend.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_category = Category::where('parent_id', 0)->orderBy('order', 'DESC')->get();
        return view('backend.category.form', compact('parent_category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request;
        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_category,
            'slug' => Str::slug($request->name),
            'status' => $request->filled('status'),
        ]);
        // upload images

        if ($request->has('image')) {
            $img = $request->file('image');
            $ext = $img->extension();
            $file = time() . '.' . $ext;
            $img->storeAs('public/category', $file); //above 4 line process the image code
            $category->image =  $file; //ai code ta image ke insert kore
        }
        $category->save();

        notify()->success('Category Successfully Added.', 'Added');
        return redirect()->route('app.category.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        $parent_category = Category::where('parent_id', 0)->orderBy('order', 'DESC')->get();
        return view('backend.category.form', compact('category', 'parent_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        // return $request;
        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_category,
            'status' => $request->filled('status'),
        ]);
        // upload images
        if ($request->hasFile('image') && request('image') != '') {
            $imagePath = public_path('storage/category/' . $category->image);
            if (File::exists($imagePath)) {
                unlink($imagePath);
            }
            $img = $request->file('image');
            $ext = $img->extension();
            $file = time() . '.' . $ext;
            $img->storeAs('public/category', $file); //above 4 line process the image code
            $category->image =  $file; //ai code ta image ke insert kore
            $category->save();
        }
        notify()->success('Category Updated Successfully.', 'Updated');
        return redirect()->route('app.category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        notify()->success("Category Successfully Deleted", "Deleted");
        return back();
    }
}
