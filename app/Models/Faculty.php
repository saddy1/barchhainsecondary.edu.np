<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = ['name', 'role', 'category', 'education', 'image', 'order', 'is_active'];

    // Helper to get image URL
    public function getImageUrlAttribute()
    {
        if (!$this->image) return asset('assets/image/default-avatar.png');
        return str_contains($this->image, 'http') ? $this->image : asset('storage/' . $this->image);
    }
}