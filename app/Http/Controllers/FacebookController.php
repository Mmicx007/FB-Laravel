<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;

class FacebookController extends Controller
{

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->user();
        // Here, you can save the user data to your database if needed.

        // Fetch Facebook Pages (you'll need to implement this part)
        $facebookPages = $this->fetchFacebookPages($user->token);

        return view('facebook.pages', ['pages' => $facebookPages]);
    }

}
