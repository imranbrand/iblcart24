<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * slug => page title
     */
    private const PAGES = [
        'about'    => 'About Us',
        'contact'  => 'Contact Us',
        'privacy'  => 'Privacy Policy',
        'terms'    => 'Terms & Conditions',
        'returns'  => 'Return & Refund Policy',
        'shipping' => 'Shipping & Delivery',
        'faq'      => 'Frequently Asked Questions',
    ];

    public function show(string $slug)
    {
        abort_unless(isset(self::PAGES[$slug]), 404);

        return view('pages.show', [
            'slug'  => $slug,
            'title' => self::PAGES[$slug],
        ]);
    }
}