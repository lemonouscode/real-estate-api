<?php

namespace App\Http\Controllers;

use App\Models\carouselImage;
use App\Models\galleryImage;
use App\Models\Villa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

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

        // dd($req->file('featured_image'));


        $data = $req->validate([
            'title'=>'required|string',
            'price'=>'required|integer',
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

            $featuredImageName = $data['title'].'-image-'.time().rand(1,1000).'.'.$featured_image->extension();
            $featured_image->move(public_path('villa_images'),$featuredImageName);

            $featuredImagePath = 'product_images/' . $featuredImageName;
            $fullImagePath = $baseUrl . '/' . $featuredImagePath; // Full image URL

            $new_villa = Villa::create([
                'title' => $req->title,
                'price' => $req->price,
                'description' => $req->description,
                'beds'=> $req->beds,
                'baths' => $req->baths,
                'size' => $req->size,
                'featured_image' => $fullImagePath // Save full image path in 'img_path' column
            ]);

            // Saving carousel Images
            foreach($carouselImages as $carouselImage){
                $imageName = $data['title'].'-image-'.time().rand(1,1000).'.'.$carouselImage->extension();
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
                $imageName = $data['title'].'-image-'.time().rand(1,1000).'.'.$galleryImage->extension();
                $galleryImage->move(public_path('villa_images'), $imageName);

                $imagePath = "villa_images" . $imageName;
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
