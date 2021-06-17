<?php

namespace Blimundo\Menu;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class Builder
{
    /** @var \Blimundo\Menu\Menu */
    protected $menu;

    public function __construct(string|array $label)
    {
        $this->menu = new Menu();

        $this->menu->label = $label;
    }

    public static function add(string|array $label): self
    {
        return new self($label);    
    }

    public function icon(string $icon): self
    {
        $this->menu->icon = $icon;

        return $this;
    }

    public function order(int $order): self
    {
        $this->menu->order = $order;

        return $this;
    }

    public function gates(string ...$gates): self
    {
        $this->menu->gates = implode('|', $gates);

        return $this;
    }

    public function url(string $url): self
    {
        $this->menu->url = $url;

        return $this;
    }

    public function action(string $url): self
    {
        $this->menu->url = 'action:' . $url;

        return $this;
    }

    public function route(string $url): self
    {
        $this->menu->url = 'route:' . $url;

        return $this;
    }

    /** @throw Illuminate\Database\Eloquent\ModelNotFoundException */
    public function menu(Menu|int|string $menu): self
    {
        if (is_string($menu)) {
            $menu = Menu::where('label', 'like', "%{$menu}%")->first();

            throw_if(is_null($menu), ModelNotFoundException::class);
        }

        if ($menu instanceof Menu) {
            $this->menu->menu_id = $menu->id;
        } else {
            $this->menu->menu_id = $menu;
        }

        return $this;
    }

    public function create(): Menu
    {
        $this->menu->save();

        return $this->menu;
    }

    public function items(callable $group): Menu
    {
        DB::transaction(function () use ($group) {
            $this->create();

            $group();

            Menu::where('id', '>', $this->menu->id)
                ->update(['menu_id' => $this->menu->id]);
        });

        return $this->menu;
    }
}
