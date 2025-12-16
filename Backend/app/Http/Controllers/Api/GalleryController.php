<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;

class GalleryController extends Controller
{
    /**
     * GET /api/gallery
     */
    public function index()
    {
        $items = GalleryItem::active()->latest()->get();
        return $this->success($items);
    }
}
