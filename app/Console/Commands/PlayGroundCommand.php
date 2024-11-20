<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\BorderType;
use Spatie\Image\Enums\Orientation;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Image;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

class PlayGroundCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @throws CouldNotLoadImage
     */
    //public function handle()
    //{
    //    $imageColumn = Company::where('slug', 'houzz')->first()->logo()->first()->path;
    //
    //    $imagePath = storage_path('app/public/' . $imageColumn);
    //
    //    $optimizedImagePath = storage_path('app/public/optimized/' . basename($imageColumn));
    //
    //    if (! is_dir(dirname($optimizedImagePath))) {
    //        mkdir(dirname($optimizedImagePath), 0755, true);
    //    }
    //
    //    $manager = new ImageManager(Driver::class);
    //    $image = $manager->read($imagePath);
    //    $encoded = $image->encode(new WebpEncoder); // Intervention\Image\EncodedImage
    //
    //    $encoded->save(storage_path('app/public/' . $imageColumn . '.webp'));
    //    $image->save($optimizedImagePath . '.webp', progressive: true);
    //
    //    $encoded = $image->encode(new WebpEncoder);
    //    $encoded->save($optimizedImagePath);
    //
    //    dd('done intervention');
    //
    //    $optimizerChain = OptimizerChainFactory::create();
    //
    //    // Optimize the image and save it to the optimized directory
    //    $optimizerChain->optimize($imagePath, $optimizedImagePath);
    //
    //    //if(!is_dir($optimizedImagePath)){
    //    //    mkdir(storage_path('app/public/optimized' ));
    //    //}
    //
    //    $optimizerChain = OptimizerChainFactory::create();
    //
    //    $optimizerChain->optimize(
    //        $imagePath,
    //        $optimizedImagePath
    //    );
    //
    //    //dd('done opt');
    //
    //    $imageExtensionsWeHave = \App\Models\Image::query()
    //        ->where('path', '!=', '')
    //        ->whereNotNull('path')
    //        ->get()->map(function ($image) {
    //            return pathinfo($image->path, PATHINFO_EXTENSION);
    //        })->reject(function ($extension) {
    //            return empty($extension);
    //        })->groupBy(function ($extension) {
    //            return $extension;
    //        })->keys();
    //
    //    //dd($imagePaths);
    //
    //    // they are only jpg/png
    //
    //    $tags = Tag::select(['id', 'name'])->limit(10)->get()->toArray();
    //
    //    $oldTag = $tags[0];
    //    unset($tags[0]);
    //
    //    //dd(
    //    //    similar_text('ai', 'aii', $percent),
    //    //    $percent,
    //    //);
    //    foreach ($tags as $tag) {
    //        //if(similar_text($tag['name'], $oldTag['name'], $percent) == -1) {
    //        //    // merge tags
    //        //}
    //        $simlar = similar_text($oldTag['name'], $tag['name'], $percent);
    //
    //        if ($percent > 44) {
    //            echo 'similar ' . $tag['name'] . ' with ' . $oldTag['name'] . ' ' . round($percent, 2) . '%' . PHP_EOL;
    //        } else {
    //            //echo "not similar " . $tag['name'] . ' with ' . $oldTag['name'] .  ' '. round($percent,2) . '%'. PHP_EOL;
    //        }
    //
    //        $oldTag = $tag;
    //    }
    //
    //    dd('done');
    //    dd(
    //        $tags
    //    );
    //    dd(
    //        similar_text('insurtech', 'insurance', $percent),
    //        $percent . '%'
    //    );
    //    //dd(
    //    //    \App\Models\Image::where('path' ,'!=' , '')->count(),
    //    //    \App\Models\Image::where('path' ,'=' , '')->count(),
    //    //    \App\Models\Image::count(),
    //    //    325+569
    //    //);
    //
    //    // Generate a unique filename (you can also customize this logic)
    //    $url = 'https://dzh2zima160vx.cloudfront.net/logo/a506070fc4c6d10d3922913c2e33bdce_240_160?Expires=1861920000&Signature=WbWzC9h-MvjIDW2zBG-~0XSjeLnocJGMQm3h9lIf5fJmi7gR4gQ5YYfoZhkRxz8HCoWMgy71gd6Z1A-tWwhGTHxi3iMeUfSd4994k1cf6m8NnccA93YOLLrnJSq6acLu8Q1-Dq5sBciU1cCYxvMnSpgjwu6ixpxcJvcqljaM1QsYu~VF88fFHYnh47~HFZNfyABGoUsYpTcoawMqLivO-esDdvHupsMV3GC4XSeMKfMTRSUycmHy8toedOeDoGglnEJcYJ-3HMtb80ek3wgMk2RKW7FEsnWlhO5vISMGj6ToWHGFpssvtntzalF2QeQ-xWUHzH3hHB1Wz9HWqyanKg__&Key-Pair-Id=APKAII5OVX4LZ3WT422Q';
    //    $filename = basename($url);
    //
    //    // Use Laravel HTTP client to fetch the image
    //    $response = Http::get($url);
    //
    //    if ($response->successful()) {
    //        // Save the image to the storage
    //        $image = $response->body(); // The raw binary content of the image
    //        //$path = 'images/' . $filename; // Define the path to save the image
    //
    //        $path = storage_path("app/public/{$filename}");
    //
    //        //dd($path);
    //
    //        // Save to storage
    //        Storage::disk('public')->put(storage_path('app/public'), $image);
    //
    //        return "Image saved as {$path}";
    //    } else {
    //        return 'Failed to download the image.';
    //    }
    //
    //    //dd(
    //    //    parse_url('aviv.com/people/monte-magdy')
    //    //    , Str::isUrl('https://www.aviv.com/people/monte-magdy')
    //    //
    //    //);
    //
    //    $path = storage_path('app/public/01JCV3NYV484Q5FSNM9CDNRMBK.png');
    //    $path2 = storage_path('app/public/saved.png');
    //
    //    $optimizerChain = (new OptimizerChain)
    //        ->addOptimizer(new Jpegoptim([
    //            '--strip-all',
    //            '--all-progressive',
    //            '-m85',
    //        ]))
    //        ->setTimeout(90);
    //
    //    $pathTest = storage_path('app/public/cfc22e766888361fb24493e8c17f6e78_192_160');
    //
    //    dd(
    //        filetype($pathTest),
    //        mime_content_type($pathTest),
    //        pathinfo($pathTest),
    //    );
    //    dd(
    //        Image::load($path)
    //
    //            //->text('Hi there', 100, '#ffffff', x: 10, y: 1523)
    //            //->watermark('https://t3.ftcdn.net/jpg/02/44/83/32/360_F_244833214_bBmRijbyEmtKrm7Q5zdcMc4ks3tpTmVu.jpg', AlignPosition::Bottom, alpha: 50)
    //            //->optimize($optimizerChain)
    //            //    ->contrast(20)
    //            //    ->blur(50)
    //            //    ->pixelate(10)
    //            //    ->orientation(Orientation::Rotate180)
    //            //->border(15, BorderType::Shrink, '007698')
    //            ->save($path2)
    //    );
    //}
}
