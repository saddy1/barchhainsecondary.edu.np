<?php

namespace App\Http\Controllers;
use App\Models\Media;

class GalleryController extends Controller
{
    public function index()
    {
        $galleryImages = Media::latest()->get();
        return view('pages.gallery', compact('galleryImages'));
    }
}
