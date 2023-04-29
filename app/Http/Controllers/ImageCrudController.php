<?php

namespace App\Http\Controllers;

use App\Models\ImageCrud;
use Illuminate\Http\Request;
use Session;
use File;

class ImageCrudController extends Controller
{
    public function all_products(){
        $products=ImageCrud::all();
        return view('products',compact('products'));
    }
    public function add_new_product(){
        return view('add_new_product');
    }
    public function store_product(Request $request){
        $request->validate([
            'name'=> 'required',
            'image'=>'required|mimes:png,jpg,jpeg',
        ]);
        $imageName="";
        if($image=$request->file('image')){
            $imageName=time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('images/products',$imageName);
        }
        ImageCrud::create([
            'name'=>$request->name,
            'image'=>$imageName,
        ]);
        Session::flash('message','Product Added Successfully.');
        return redirect()->back();
    }
    public function edit_product($id){
        $product=ImageCrud::findOrFail($id);
        return view('edit_product',compact('product'));
    }

    public function update_product(Request $request,$id){
        $product=ImageCrud::findOrFail($id);
        $request->validate([
            'name'=> 'required',            
        ]);
        $imageName='';
        $deleteOldImage='images/products/'.$product->image;
        if($image=$request->file('image')){
            if(file_exists($deleteOldImage)){
                File::delete($deleteOldImage);
            }
            $imageName=time().'-'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->move('images/products',$imageName);
        }else{
            $imageName=$product->image;
        }
        ImageCrud::where('id',$id)->update([
            'name'=>$request->name,
            'image'=>$imageName,
        ]);
        Session::flash('message','Product Updated Successfully.');
        return redirect()->back();
    }
    public function delete_product($id){
        $product=ImageCrud::findOrFail($id);
        $deleteImage='images/products/'.$product->image;
        if(file_exists($deleteImage)){
            File::delete($deleteImage);            
        }
        $product->delete();
        Session::flash('message','Product Deleted Successfully.');
        return redirect()->back();

    }
}
