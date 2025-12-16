<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGalleryItemRequest;
use App\Models\GalleryItem;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * POST /api/admin/gallery
     */
    public function store(StoreGalleryItemRequest $request)
    {
        $imagePath = $request->file('image')->store('uploads', 'public');

        $galleryItem = GalleryItem::create([
            'title' => $request->title,
            'image_path' => $imagePath,
            'active' => $request->boolean('active', true),
        ]);

        return $this->success([
            'gallery_item' => $galleryItem,
            'message' => 'Imagen subida exitosamente',
        ], 201);
    }

    /**
     * DELETE /api/admin/gallery/{id}
     */
    public function destroy(GalleryItem $galleryItem)
    {
        // The model observer will handle file deletion
        $galleryItem->delete();

        return $this->success(['message' => 'Imagen eliminada exitosamente']);
    }
}
