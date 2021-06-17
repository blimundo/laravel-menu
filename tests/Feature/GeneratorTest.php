<?php

namespace Blimundo\Menu\Tests;

use Blimundo\Menu\Builder;
use Blimundo\Menu\Generator;
use Blimundo\Menu\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Builder::add('Home')->create();

        Builder::add('Countries')->items(function () {
            Builder::add('Cabo Verde')->url('google.com?q=cabo+verde')->order(1)->create();
            Builder::add('Portugal')->url('google.com?q=portugal')->order(2)->create();
        });

        Builder::add('Settings')->items(function () {
            Builder::add('Users')->url('#')->create();
            Builder::add('Roles')->url('#')->create();
        });
    }

    /** @test */
    public function can_generate_menu(): void
    {
        $menu = Generator::generate();

        $this->assertIsArray($menu);
        $this->assertIsArray(array_pop($menu));
    }

    /**
     * @test
     * @depends can_generate_menu
     */
    public function is_sorting_by_order_and_by_label(): void
    {
        $menu = Generator::generate();

        $keys = array_keys($menu);

        $this->assertEquals('Countries', $keys[0]);
        $this->assertEquals('Home', $keys[1]);
        $this->assertEquals('Settings', $keys[2]);

        $keys = array_keys($menu['Settings']['items']);

        $this->assertEquals('Roles', $keys[0]);
        $this->assertEquals('Users', $keys[1]);
    }
}
