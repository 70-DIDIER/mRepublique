<?php

namespace App\Http\Controllers\Admin;

use App\Models\Carousel;
use Illuminate\Http\Request;
use App\Services\CarouselService;
use App\Http\Controllers\Controller;

class CarouselController extends Controller
{
    protected $carouselService;
    public function __construct(CarouselService $carouselService)
    {
        $this->carouselService = $carouselService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $carousels = Carousel::all();
        return view('carousels.index', compact('carousels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('carousels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
        ]);
         // Gérer l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $this->carouselService->optimize($request->file('image'));
            $imagePath = 'images/' . time() . '.jpg';
            $this->carouselService->save($image, storage_path('app/public/' . $imagePath));
        }
        // Créer le carousel
        Carousel::create([
            'titre' => $request->input('titre'),
            'image' => $imagePath,
        ]);
        return redirect()->route('carousels.index')->with('success', 'Carousel créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
