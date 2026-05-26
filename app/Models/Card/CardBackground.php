<?php

namespace App\Models\Card;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 * @property string $org_type
 * @property string $member_type
 * @property string $file_path
 * @property bool   $is_active
 */
class CardBackground extends Model
{
    protected $fillable = ['name', 'org_type', 'member_type', 'file_path', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function activeFor(string $orgType, string $memberType): ?self
    {
        return static::where('org_type', $orgType)
            ->where('member_type', strtolower($memberType))
            ->where('is_active', true)
            ->first();
    }
}
