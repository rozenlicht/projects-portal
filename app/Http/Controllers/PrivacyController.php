<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->get('lang', 'en');
        
        // Validate language parameter
        if (!in_array($lang, ['en', 'nl'])) {
            $lang = 'en';
        }
        
        return view("privacy.{$lang}");
    }
}
