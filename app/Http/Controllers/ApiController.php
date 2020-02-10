<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Movie;
class ApiController extends Controller
{
    public function createMovie(Request $request) {
        
        $this->validate($request, [
            'title'=>'required',
            'cover'=>'image|nullable|max:1999',
            'genre'=>'required',
            'description'=>'required',
            'country'=>'required'
        ]);
        
        if($request->hasFile('cover')){
            $filenameWithExt = $request->file('cover')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('cover')->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('cover')->storeAs('public/covers',$fileNameToStore);
        }else{

            $fileNameToStore = 'noimage.jpg';
        }
        
        $movies = new Movie();
        $movies->title = $request->input('title');
        $movies->cover = $fileNameToStore;
        $movies->genre = $request->input('genre');
        $movies->description = $request->input('description');
        $movies->country = $request->input('country');
        $movies->save();

        return response()->json([
            "message" => "Movie record successfully created!"
        ], 201);
      }

      public function getAllMovies(Request $request) {
        $movies = Movie::get()->toJson(JSON_PRETTY_PRINT);
        return response($movies, 200);
      }
      
      public function getMovie($title) {
        if (Movie::where('title','LIKE', '%{$searchTerm}%')->exists()) {
            $movies = Movie::where('title', 'LIKE', '%{$searchTerm}%')->get()->toJson(JSON_PRETTY_PRINT);
            return response($movies, 200);
          } else {
            return response()->json([
              "message" => "Movie not found!"
            ], 404);
          }
      }
      
      public function updateMovie(Request $request, $id) {
        if (Movie::where('id', $id)->exists()) {
            $this->validate($request, [
            'title'=>'required',
            'cover'=>'image|nullable|max:1999',
            'genre'=>'required',
            'description'=>'required',
            'country'=>'required'
        ]);
        
        if($request->hasFile('cover')){
            $filenameWithExt = $request->file('cover')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('cover')->getClientOriginalExtension();
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            $path = $request->file('cover')->storeAs('public/covers',$fileNameToStore);
        }else{

            $fileNameToStore = 'noimage.jpg';
        }
            $movies = Movie::find($id);
            $movies->title = $request->input('title');
            $movies->cover = $fileNameToStore;
            $movies->genre = $request->input('genre');
            $movies->description = $request->input('description');
            $movies->country = $request->input('country');
            $movies->save();
    
            return response()->json([
                "message" => "Records updated successfully!"
            ], 200);
        }else{
              return response()->json([
                "message" => "Movie doesn't exist!"
            ], 404);
            
        }
    }
    
    public function deleteMovie($id) {
        if(Movie::where('id', $id)->exists()) {
          $movies = Movie::find($id);
          
          if($movies->cover != 'noimage.jpg'){
                Storage::delete('public/covers/'.$movies->cover);
            }
          
          $movies->delete();

          return response()->json([
            "message" => "Record succesfully deleted!"
          ], 202);
        } else {
          return response()->json([
            "message" => "Movie doesn't exist!"
          ], 404);
        }
      }
}
