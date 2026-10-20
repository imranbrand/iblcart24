<?php

use App\Services\ImageOptimizer;
use Illuminate\Http\UploadedFile;

test('tracking scripts stay hidden when no ids are set', function () {
    config(['shop.fb_pixel_id' => '', 'shop.ga_id' => '']);

    $this->get(route('home'))->assertOk()->assertDontSee('fbevents.js', false);
});

test('pixel and analytics print when ids are set', function () {
    config(['shop.fb_pixel_id' => '123456789012345', 'shop.ga_id' => 'G-TEST1234']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('fbevents.js', false)
        ->assertSee('G-TEST1234', false);
});

test('image optimizer shrinks big photos to webp', function () {
    if (! function_exists('imagewebp')) {
        $this->markTestSkipped('GD with WebP support is not installed.');
    }

    $dir = sys_get_temp_dir() . '/ibl_' . uniqid();
    mkdir($dir);

    $name = app(ImageOptimizer::class)->save(
        UploadedFile::fake()->image('big.png', 2400, 1200),
        $dir,
        'big.png'
    );

    expect($name)->toEndWith('.webp');
    expect(getimagesize($dir . '/' . $name)[0])->toBe(1200);

    array_map('unlink', glob($dir . '/*'));
    rmdir($dir);
});
