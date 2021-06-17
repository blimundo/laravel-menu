<?php

namespace Blimundo\Menu;

use Blimundo\Menu\Menu;
use Illuminate\Database\Eloquent\Collection;

class Generator
{
    public static function generate(): array
    {
        return (new self())->get();
    }

    public function get(): array
    {
        $menus = Menu::with('menus')->whereNull('menu_id')->get();

        return $this->proccess($menus);
    }

    protected function proccess(Collection $menus, int $level = 1): array
    {
        return $menus->filter(fn ($item) => $item->canAccess())
            ->sort($this->getSortFunction())
            ->mapWithKeys($this->getMapFunction($level))
            ->toArray();
    }

    protected function getSortFunction(): callable
    {
        return function ($a, $b) {
            if ($a->order === $b->order) {
                return strcmp($a->label, $b->label);
            }

            if (is_null($a->order) && !is_null($b->order)) {
                return 1;
            }

            if (is_null($b->order) && !is_null($a->order)) {
                return -1;
            }

            return $a->order < $b->order ? -1 : 1;
        };
    }

    protected function getMapFunction(int $level): callable
    {
        return function ($item) use ($level) {
            $data = [
                'icon' => $item->icon,
                'label' => $item->label,
                'link' => $item->link,
                'level' => $level,
            ];

            if ($item->menus->count() > 0) {
                $data['has_items'] = true;
                $data['items'] = $this->proccess($item->menus, $level + 1); 
            } else {
                $data['has_items'] = false;
            }

            return [$item->label => $data];
        };
    }
}
