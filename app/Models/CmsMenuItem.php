<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsMenuItem extends Model
{
    protected $fillable = [
        'cms_menu_id',
        'parent_id',
        'cms_page_id',
        'label',
        'subtitle',
        'type',
        'url',
        'target',
        'sort_order',
        'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function menu()
    {
        return $this->belongsTo(CmsMenu::class, 'cms_menu_id');
    }

    public function page()
    {
        return $this->belongsTo(CmsPage::class, 'cms_page_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->where('is_active', true)->orderBy('sort_order')->orderBy('label')->with('children.page', 'page');
    }

    public function getResolvedUrlAttribute(): string
    {
        return $this->type === 'page' && $this->page ? $this->page->url : ($this->url ?: '#');
    }
}
