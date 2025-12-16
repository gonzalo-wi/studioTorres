<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create storage directory if it doesn't exist
        $uploadsPath = storage_path('app/public/uploads');
        if (!File::exists($uploadsPath)) {
            File::makeDirectory($uploadsPath, 0755, true);
        }

        // Create placeholder images
        $items = [
            [
                'title' => 'Corte Clásico',
                'image_path' => 'uploads/placeholder-1.jpg',
                'active' => true,
            ],
            [
                'title' => 'Estilo Moderno',
                'image_path' => 'uploads/placeholder-2.jpg',
                'active' => true,
            ],
            [
                'title' => 'Barba Profesional',
                'image_path' => 'uploads/placeholder-3.jpg',
                'active' => true,
            ],
        ];

        foreach ($items as $item) {
            // Create a simple 1-pixel placeholder file
            $imagePath = storage_path('app/public/' . $item['image_path']);
            if (!File::exists($imagePath)) {
                // Create directory if needed
                File::makeDirectory(dirname($imagePath), 0755, true, true);
                
                // Create a minimal valid JPEG header (1x1 pixel black image)
                $minimalJpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAYEBQYFBAYGBQYHBwYIChAKCgkJChQODwwQFxQYGBcUFhYaHSUfGhsjHBYWICwgIyYnKSopGR8tMC0oMCUoKSj/2wBDAQcHBwoIChMKChMoGhYaKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCj/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA//2Q==');
                file_put_contents($imagePath, $minimalJpeg);
            }

            GalleryItem::create($item);
            $this->command->info('✓ Gallery item created: ' . $item['title']);
        }

        $this->command->warn('⚠ Remember to replace placeholder images with real barbershop photos!');
    }
}
