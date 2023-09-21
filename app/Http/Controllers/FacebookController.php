<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Facebook\Facebook;
use App\Models\User;
use App\Models\UserFacebookPage;
use App\Models\Campaign;
use App\Models\Ads;



class FacebookController extends Controller
{

    public function redirectToFacebook()
    {
        $permissions = [
            'email', 'pages_manage_posts', 'pages_manage_engagement',
            'pages_manage_metadata', 'pages_read_engagement', 'pages_show_list',
            'pages_messaging', 'public_profile', 'read_insights'
        ];
        return Socialite::driver('facebook')
        ->scopes($permissions)
        ->redirect();
    }

    public function handleFacebookCallback()
    {
        $socialiteUser = Socialite::driver('facebook')->user();
        
        $user = User::where('facebook_id', $socialiteUser->id)
                ->orWhere('email', $socialiteUser->email)
                ->first();
        if (!$user) {
            $user = new User();
            $user->facebook_id = $socialiteUser->id;
            $user->name = $socialiteUser->name;
            $user->email = $socialiteUser->email;
            $user->access_token = $socialiteUser->token;
            $user->email_verified_at = Carbon::now();
            $user->created_at = Carbon::now();
            $user->updated_at = Carbon::now();
            $user->save();
        } else{
            $user->update([
                'access_token' => $socialiteUser->token,
                'name' => $socialiteUser->name,
                'email' => $socialiteUser->email,
                'updated_at' => Carbon::now()
            ]);
        }

        Auth::login($user);

        $this->getPages();

        return redirect()->route('dashboard');
    }

    public function getPages()
    {
        $fb = new Facebook([
            'app_id' => config('services.facebook.client_id'),
            'app_secret' => config('services.facebook.client_secret'),
            'default_graph_version' => 'v16.0',
        ]);

        $accessToken = Auth::user()->access_token;

        $fb->setDefaultAccessToken($accessToken);

        try {
            $response = $fb->get('/me/accounts?fields=cover,emails,picture,id,name,url,username,access_token&limit=400');
            $pages = $response->getGraphList()->asArray();
            UserFacebookPage::where('user_id', Auth::id())->delete();
            foreach ($pages as $page) {
                UserFacebookPage::create([
                    'user_id' => Auth::id(),
                    'page_id' => $page['id'],
                    'name' => $page['name'],
                    'cover_url' => $page['cover']['source'],
                    'email' => isset($page['emails'][0]) ? $page['emails'][0] : null,
                    'username' => isset($page['username']) ? $page['username'] : null,
                    'access_token' => $page['access_token'],
                ]);
            }
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            return redirect()->route('fb.login');
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return redirect()->route('fb.login');
        }
    }

    public function PagesByUser() {
        $pages = UserFacebookPage::where('user_id', Auth::id())->get();
        return view('pages.index', ['pages' => $pages]);
    }

}
