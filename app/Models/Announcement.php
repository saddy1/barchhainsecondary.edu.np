<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'type', 'category', 'content', 'excerpt', 
        'image_type', 'featured_image', 'event_date', 'event_time', 
        'event_location', 'is_published'
    ];

    // Helper to get the correct image URL for the frontend
 // Helper to get the correct image URL for the frontend
    public function getImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return asset('assets/image/default-placeholder.jpg'); // Change this to your default image
        }

        if ($this->image_type === 'link') {
            return $this->featured_image; // Direct Drive/External Link
        }

        // Changed from asset('storage/' ...) to asset(...) because it's directly in the public folder now
        return asset($this->featured_image); 
    }
}