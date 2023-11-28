<?php

namespace App\Http\Controllers;

use App\Models\carouselImage;
use App\Models\galleryImage;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;

class VillaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // $villas = Villa::all();

        $villas = Villa::with('carouselImages', 'galleryImages')->get();

        return response()->json(['villas' => $villas]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $req)
    {
        //

        $data = $req->validate([
            'title'=>'required|string',
            'price'=>'required|integer',
            'address'=> 'required|string',
            'description'=> 'required|string',
            'beds'=> 'required|integer',
            'baths'=> 'required|integer',
            'size'=> 'required|integer',
            'featured_image.*' => 'required|image|mimes:jpeg,png,jpg,gif',
            'carousel_image.*' => 'required|image|mimes:jpeg,png,jpg,gif',
            'gallery_image.*' => 'required|image|mimes:jpeg,png,jpg,gif'
        ]);


        $baseUrl = URL::to('/'); // Retrieve base URL dynamically

        // Write all login under one if use AND operator
        if($req->hasFile('featured_image') && $req->hasFile('carousel_image') && $req->hasFile('gallery_image')){
            $featured_images = $req->file('featured_image');
            $carouselImages = $req->file('carousel_image');
            $galleryImages = $req->file('gallery_image');

            // \Log::info('Featured Image Debug:', ['featured_image' => $featured_image]);

            $featured_image = $featured_images[0];


            $featuredImageName = str_replace(" ","-",$data['title']).'-image-'.time().rand(1,1000).'.'.$featured_image->extension();
            $featured_image->move(public_path('villa_images'),$featuredImageName);

            $featuredImagePath = 'villa_images/' . $featuredImageName;
            $fullImagePath = $baseUrl . '/' . $featuredImagePath; // Full image URL

            // Creating slug for villa
            $slug = strtolower(str_replace(" ","-",$req->title));
            

            $new_villa = Villa::create([
                'title' => $req->title,
                'slug' => $slug,
                'featured' => $req->featured,
                'address' => $req->address,
                'price' => $req->price,
                'description' => $req->description,
                'beds'=> $req->beds,
                'baths' => $req->baths,
                'size' => $req->size,
                'featured_image' => $fullImagePath // Save full image path in 'img_path' column
            ]);

            // Saving carousel Images
            foreach($carouselImages as $carouselImage){

                
                $imageName = str_replace(" ","-",$data['title']).'-image-'.time().rand(1,1000).'.'.$carouselImage->extension();
                $carouselImage->move(public_path('villa_images'),$imageName);

                $imagePath = 'villa_images/' . $imageName;
                $fullImagePath = $baseUrl . '/' . $imagePath; // Full image URL

                carouselImage::create([
                    'villa_id' => $new_villa->id,
                    'name' => $imageName,
                    'img_path' => $fullImagePath
                ]);

            }

            //Saving gallery Images
            foreach($galleryImages as $galleryImage){

                $imageName = str_replace(" ","-",$data['title']).'-image-'.time().rand(1,1000).'.'.$galleryImage->extension();
                $galleryImage->move(public_path('villa_images'), $imageName);

                $imagePath = "villa_images/" . $imageName;
                $fullImagePath = $baseUrl . '/' . $imagePath;

                galleryImage::create([
                    'villa_id' => $new_villa->id,
                    'name' => $imageName,
                    'img_path' => $fullImagePath
                ]);
            }

            
    
        }

        return response()->json(['message' => 'Added successfully']);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function getVillaBySlug(string $slug){
        
        $villa = Villa::with('carouselImages', 'galleryImages')
                ->where('slug', $slug)
                ->first();

        if ($villa) {
            return $villa;
        } else {
            return response()->json(['message' => 'Villa not found'], 404);
        }
    }


    public function update(Request $req, string $slug)
    {
        // This retrieves the villa
        $villa = Villa::with('carouselImages', 'galleryImages')
            ->where('slug', $slug)
            ->first();

        $baseUrl = URL::to('/'); // Retrieve base URL dynamically

        $featured_images = $req->file('replace_featured_image');

        $carouselImages = "";
        $galleryImages = "";
        
        // Deleting villa image file from public folder
        $currentVillaFeaturedImageFullPath = $villa->featured_image;
        $currentVillaFeaturedImagefilename = basename(parse_url($currentVillaFeaturedImageFullPath, PHP_URL_PATH));
        $imageToDelete = public_path('villa_images/' . $currentVillaFeaturedImagefilename); 

        File::delete($imageToDelete);

        $featured_image = $featured_images[0];
        

        // return response()->json(['message' => $villa->featured_image], 404);

        // Saving a new image into table replacing the old one
        $featuredImageName = str_replace(" ","-",$req['title']).'-image-'.time().rand(1,1000).'.'.$featured_image->extension();
        $featured_image->move(public_path('villa_images'),$featuredImageName);

        $featuredImagePath = 'villa_images/' . $featuredImageName;
        $fullImagePath = $baseUrl . '/' . $featuredImagePath; // Full image URL



        // Check if the villa exists
        if ($villa) {
            // Update the villa if needed
            $villa->title = $req->title; // Replace 'New Title' with the desired new title
            $slug = strtolower(str_replace(" ","-",$req->title));
            $villa->slug = $slug;
            $villa->address = $req->address;
            $villa->price = $req->price;
            $villa->description = $req->description;
            $villa->beds = $req->beds;
            $villa->baths = $req->beds;
            $villa->size = $req->beds;
            $villa->featured_image =  $fullImagePath;

            $villa->save();

            // Return the updated villa or a response
            // return response()->json(['villa' => $villa]);
        } else {
            // If the villa is not found, return a 404 response
            return response()->json(['message' => 'Villa not found'], 404);
        }


        // Removing old carousel images from folder and table and adding new ones
        if($req->has('replace_carousel_image')){
            $carouselImages = $req->file('replace_carousel_image');

            // Get Images from model
            $villaCarouselImages = $villa->carouselImages;


            foreach($villaCarouselImages as $carouselImage){

                // Remove image from local storage (public folder)
                $currentVillaCarouselImageFullPath = $carouselImage->img_path;
                $currentVillaCarouselImagefilename = basename(parse_url($currentVillaCarouselImageFullPath, PHP_URL_PATH));
                $imageToDelete = public_path('villa_images/' . $currentVillaCarouselImagefilename); 
                File::delete($imageToDelete);

                // Remove image from table
                $carouselImage->delete();
            }

            // Save new Images
            foreach($carouselImages as $carouselImage){
                $imageName = $req['title'].'-image-'.time().rand(1,1000).'.'.$carouselImage->extension();
                $carouselImage->move(public_path('villa_images'),$imageName);

                $imagePath = 'villa_images/' . $imageName;
                $fullImagePath = $baseUrl . '/' . $imagePath; // Full image URL

                carouselImage::create([
                    'villa_id' => $villa->id,
                    'name' => $imageName,
                    'img_path' => $fullImagePath
                ]);
            }
        }
        // Adding new carousel Images to table and into villa_images folder
        else
        {
            $carouselImages = $req->file('carousel_image');

            foreach($carouselImages as $carouselImage){
                $imageName = $req['title'].'-image-'.time().rand(1,1000).'.'.$carouselImage->extension();
                $carouselImage->move(public_path('villa_images'),$imageName);

                $imagePath = 'villa_images/' . $imageName;
                $fullImagePath = $baseUrl . '/' . $imagePath; // Full image URL

                carouselImage::create([
                    'villa_id' => $villa->id,
                    'name' => $imageName,
                    'img_path' => $fullImagePath
                ]);
            }
        }


        // Removing old gallery images from folder and table and adding new ones
        if($req->has('replace_gallery_image')){
            $galleryImages = $req->file('replace_gallery_image');

            // Get Images from model
            $villaGalleryImages = $villa->galleryImages;


            foreach($villaGalleryImages as $galleryImage){

                // Remove image from local storage (public folder)
                $currentVillaGalleryImageFullPath = $galleryImage->img_path;
                $currentVillaGalleryImagefilename = basename(parse_url($currentVillaGalleryImageFullPath, PHP_URL_PATH));
                $imageToDelete = public_path('villa_images/' . $currentVillaGalleryImagefilename); 
                File::delete($imageToDelete);

                // Remove image from table
                $galleryImage->delete();
            }

            // Save new Images
            foreach($galleryImages as $galleryImage){
                $imageName = $req['title'].'-image-'.time().rand(1,1000).'.'.$galleryImage->extension();
                $galleryImage->move(public_path('villa_images'),$imageName);

                $imagePath = 'villa_images/' . $imageName;
                $fullImagePath = $baseUrl . '/' . $imagePath; // Full image URL

                galleryImage::create([
                    'villa_id' => $villa->id,
                    'name' => $imageName,
                    'img_path' => $fullImagePath
                ]);
            }
        }
        // Adding new gallery Images to table and into villa_images folder
        else
        {
            $galleryImages = $req->file('gallery_image');

            foreach($galleryImages as $galleryImage){
                $imageName = $req['title'].'-image-'.time().rand(1,1000).'.'.$galleryImage->extension();
                $galleryImage->move(public_path('villa_images'),$imageName);

                $imagePath = 'villa_images/' . $imageName;
                $fullImagePath = $baseUrl . '/' . $imagePath; // Full image URL

                galleryImage::create([
                    'villa_id' => $villa->id,
                    'name' => $imageName,
                    'img_path' => $fullImagePath
                ]);
            }
        }

        

    }

    public function getFeaturedVillas(){
        $villa = Villa::with('carouselImages', 'galleryImages')
            ->where('featured', 'true')
            ->get();

        return response()->json(['Featured_Villas' => $villa]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        // Find the villa by slug
        $villa = Villa::where('slug', $slug)->first();

        // Check if the villa exists
        if ($villa) {
            // Delete associated images if needed
            // For example, if you want to delete associated images from relationships
            $villa->carouselImages()->delete();
            $villa->galleryImages()->delete();

            // Delete the villa
            $villa->delete();

            return response()->json(['message' => 'Villa deleted successfully']);
        }

        // If the villa with the given slug does not exist
        return response()->json(['message' => 'Villa not found'], 404);
    }
}
