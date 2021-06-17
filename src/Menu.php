<?php

namespace Blimundo\Menu;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Menu extends Model
{
    use HasTranslations;

    /** @var array */
    protected $fillable = [
        'menu_id',
        'label',
        'gates',
        'icon',
        'url',
        'order',
    ];

    /** @var array */
    public $translatable = ['label'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(static::class);
    }

    public function getLinkAttribute(): ?string
    {
        if (is_null($this->attributes['url'])) {
            return '#';
        }

        $url = $this->attributes['url'];

        if (Str::startsWith($url, 'route:')) {
            return route(Str::after($url, 'route:'));
        }

        if (Str::startsWith($url, 'action:')) {
            return action(Str::after($url, 'action:'));
        }

        return $this->attributes['url'];
    }

    public function canAccess(): bool
    {
        if (empty($this->attributes['gates'])) {
            return true;
        }

        if (!Auth::check()) {
            return false;
        }
        
        $gates = explode('|', $this->attributes['gates']);

        foreach ($gates as $gate) {
            if (!Auth::user()->can($gate)) {
                return false;
            }
        }

        return true;
    }
}
