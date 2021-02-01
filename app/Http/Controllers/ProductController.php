<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy('id', 'desc')->get();
        return response()->json(['products' => $products]);
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
            'title' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
        ]);

        $product = new Product;
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;

        $directoryPath = '/assets/uploads/';
        
        if(isset($request->image)){
            $file_code = explode(',', $request['image']);
            $decode = base64_decode($file_code[1]);
             $extension =  $file_code[0];
             $extract_file_extension = explode('/', $extension);
             $mime_type = explode(';',$extract_file_extension[1]);
             $filename = Str::random(10).'.'.$mime_type[0];
             $path = public_path().$directoryPath.$filename;
             file_put_contents($path, $decode);
            $product->image = $directoryPath.$filename;
          }

        if($product->save()) {
            return response()->json(
                [ 'status' => 1,
                  'message' => 'product create successfully.',
                  'data' => $product ]
            );
        }else {
            return response()->json(
                [ 'status' => 0,
                  'message' => 'product doesn\'t create successfully.',
                  'data' => $product ]
            );
        }
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'price' => 'required',
        ]);

        $product = Product::find($id);
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        $directoryPath = '/assets/uploads/';

        if($product->image != $request->image){
            $currentFile = $product->image;
            $delete_file = public_path().$currentFile;
             if(file_exists($delete_file)){
                 @unlink($delete_file);
             }
            $file_code = explode(',', $request->image);
            $decode = base64_decode($file_code[1]);
            $extension =  $file_code[0];
            $extract_file_extension = explode('/', $extension);
            $mime_type = explode(';',$extract_file_extension[1]);
            $filename = Str::random(10).'.'.$mime_type[0];
            $path = public_path().$directoryPath.$filename;
            file_put_contents($path, $decode);
            $product->image = $directoryPath.$filename;
         }

         if($product->save()) {
            return response()->json(
                [ 'status' => 1,
                  'message' => 'product update successfully.',
                  'data' => $product ]
            );
        }else {
            return response()->json(
                [ 'status' => 0,
                  'message' => 'product doesn\'t update successfully.',
                  'data' => $product ]
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $image = $product->image ? $product->image : null;
        $image_path_full = public_path().$image;

        if($product->delete()) {
            if(file_exists($image_path_full)) {

                @unlink($image_path_full);
                    
            }

            return response()->json(
                [ 'status' => 1,
                  'message' => 'product delete successfully.',
                  'data' => $product ]
            );
        }else {
            return response()->json(
                [ 'status' => 0,
                  'message' => 'product doesn\'t delete.',
                  'data' => $product ]
            );
        }
    }
}
