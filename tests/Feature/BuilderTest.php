<?php

namespace Blimundo\Menu\Tests;

use Blimundo\Menu\Builder;
use Blimundo\Menu\Menu;
use Blimundo\Menu\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuilderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_a_new_menu_item(): void
    {
        Builder::add('Test')->create();

        $this->assertDatabaseCount('menus', 1);
        $this->assertDatabaseHas('menus', [
            'label' => json_encode(['en' => 'Test'])
        ]);
    }

    /**
     * @test
     * @depends can_create_a_new_menu_item
     */
    public function is_returning_menu_model(): void
    {
        $result = Builder::add('Test')->create();

        $this->assertInstanceOf(Menu::class, $result);
    }

    /**
     * @test
     * @depends can_create_a_new_menu_item
     */
    public function can_set_order(): void
    {
        Builder::add('Test')->order(10)->create();

        $this->assertEquals(10, Menu::first()->order);
    }

    /**
     * @test
     * @depends can_create_a_new_menu_item
     */
    public function can_set_icon(): void
    {
        Builder::add('Test')->icon('mdi mdi-home')->create();

        $this->assertEquals('mdi mdi-home', Menu::first()->icon);
    }

    /**
     * @test
     * @depends can_create_a_new_menu_item
     */
    public function can_set_gates(): void
    {
        Builder::add('Test')->gates('gate1')->create();

        $this->assertEquals('gate1', Menu::find(1)->gates);

        Builder::add('Test')->gates('gate2', 'gate3')->create();

        $this->assertEquals('gate2|gate3', Menu::find(2)->gates);
    }

    /**
     * @test
     * @depends can_create_a_new_menu_item
     */
    public function can_set_url(): void
    {
        Builder::add('Test')->url('https://google.com')->create();

        $this->assertEquals('https://google.com', Menu::find(1)->url);

        Builder::add('Test')->action('HomeController@show')->create();

        $this->assertEquals('action:HomeController@show', Menu::find(2)->url);

        Builder::add('Test')->route('home')->create();

        $this->assertEquals('route:home', Menu::find(3)->url);
    }

    /**
     * @test
     * @depends can_create_a_new_menu_item
     */
    public function can_add_submenu(): void
    {
        $menu = Builder::add('Test')->items(function () {
            Builder::add('Alpha')->order(1)->create();
            Builder::add('Bravo')->order(2)->create();
        });

        $this->assertDatabaseCount('menus', 3);
        $this->assertEquals(2, $menu->menus()->count());

        $this->assertInstanceOf(Menu::class, $menu);

        $item = Menu::find(2);

        $this->assertEquals(1, $item->order);
        $this->assertEquals('Alpha', $item->label);
        $this->assertEquals(0, $item->menus()->count());
    }

    /**
     * @test
     * @depends can_create_a_new_menu_item
     */
    public function can_set_menu(): void
    {
        $menu = Builder::add('Test')->create();

        Builder::add('One')->menu($menu)->create();
        Builder::add('Two')->menu(1)->create();
        Builder::add('Three')->menu('Test')->create();

        $this->assertDatabaseCount('menus', 4);
        $this->assertCount(3, $menu->menus);
    }

    /**
     * @test
     * @depends can_set_menu
     */
    public function is_throwing_exception_when_menu_doesnt_exists(): void
    {
        Builder::add('Test')->create();

        Builder::add('One')->menu('Test')->create();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        
        Builder::add('One')->menu('Laravel')->create();

        $this->assertDatabaseCount('menus', 1);
    }
}
