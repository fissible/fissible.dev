<?php

namespace App\Http\Controllers;

class MarketingController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function station()
    {
        $station = collect(config('packages.paid'))
            ->firstWhere('slug', 'station');

        return view('pages.station', compact('station'));
    }

    public function toolsIndex()
    {
        return view('pages.tools.index', [
            'tuiPackages' => config('packages.tui'),
            'phpPackages' => config('packages.php'),
        ]);
    }

    public function toolShow(string $slug)
    {
        $package = collect(config('packages.tui'))
            ->merge(config('packages.php'))
            ->firstWhere('slug', $slug);

        abort_unless($package, 404);

        return view('pages.tools.show', compact('package'));
    }

    public function comingSoon(string $slug)
    {
        $product = collect(config('packages.paid'))
            ->firstWhere('slug', $slug);

        abort_unless($product, 404);

        return view('pages.coming-soon', compact('product'));
    }
}
