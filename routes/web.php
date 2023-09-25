<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CampaignController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/',[LoginController::class, 'index'])->name('fb.login');
Route::get('/facebook-login', [FacebookController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('/facebook-callback', [FacebookController::class, 'handleFacebookCallback']);
Route::middleware(['auth'])->group(function () {
    Route::get('/pages', [FacebookController::class, 'PagesByUser'])->name('pages.index');
    Route::get('/getAdAccount', [FacebookController::class, 'getAdAccounts']);
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/ads', [FacebookController::class, 'getAds'])->name('facebook.ads');


    Route::get('/campaigns/list', [CampaignController::class, 'index'])->name('campaign.index');
    Route::get('/campaigns/createad', [CampaignController::class, 'createAd'])->name('campaign.createAd');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
});

require __DIR__.'/auth.php';
