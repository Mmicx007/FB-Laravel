<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserFacebookPage;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Facebook\Facebook;

class CampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('user_page_campaign.user_id', Auth::id())
            ->join('user_facebook_pages', 'user_page_campaign.page_id', '=', 'user_facebook_pages.page_id')
            ->select('user_page_campaign.*', 'user_facebook_pages.name as page_name')
            ->get();

        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $pages = UserFacebookPage::where('user_id', auth()->user()->id)->get(); // Assuming 'Page' is your model for pages
        return view('campaigns.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'page_id' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0.01',
            'target_audience' => 'required|string|max:255',
            'ad_content' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $campaign = new Campaign;
        $campaign->page_id = $validatedData['page_id'];
        $campaign->name = $validatedData['name'];
        $campaign->budget = $validatedData['budget'];
        $campaign->target_audience = $validatedData['target_audience'];
        $campaign->ad_content = $validatedData['ad_content'];
        $campaign->start_date = $validatedData['start_date'];
        $campaign->end_date = $validatedData['end_date'];
        
        $campaign->user_id = auth()->user()->id;
        $campaign->save();
        return redirect()->route('campaign.index')->with('success', 'Campaign created successfully.');
    }

    public function show($id)
    {
        $campaign = Campaign::findOrFail($id);
        return view('campaigns.show', compact('campaign'));
    }

    public function edit($id)
    {
        $pages = UserFacebookPage::where('user_id', auth()->user()->id)->get();
        $campaign = Campaign::findOrFail($id);
        return view('campaigns.edit', compact('campaign','pages'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validatedData = $request->validate([
            'page_id' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0.01',
            'target_audience' => 'required|string|max:255',
            'ad_content' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $campaign->update([
            'page_id' => $validatedData['page_id'],
            'name' => $validatedData['name'],
            'budget' => $validatedData['budget'],
            'target_audience' => $validatedData['target_audience'],
            'ad_content' => $validatedData['ad_content'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
        ]);
        
        return redirect()->route('campaign.index')->with('success', 'Campaign Updated Successfully.');
    }

    public function destroy($id)
    {
        try {
            $campaign = Campaign::findOrFail($id);
            $campaign->delete();
            return redirect()->route('campaign.index')->with('success', 'Campaign deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('campaign.index')->with('error', 'An error occurred while deleting the campaign.');
        }
    }

}
