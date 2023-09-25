<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserFacebookPage;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Facebook\Facebook;
use FacebookAds\Api;
use FacebookAds\Object\Campaign as FacebookCampaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Object\Values\AdObjectives;
use FacebookAds\Object\Values\AdBuyingTypes;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Object\Values\BillingEvents;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use DateTime;
use FacebookAds\Object\TargetingSearch;
use FacebookAds\Object\Fields\TargetingFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Targeting;
use FacebookAds\Exceptions\FacebookAdsException;
use FacebookAds\Object\AdImage;
use FacebookAds\Object\Fields\AdImageFields;
use FacebookAds\Logger\CurlLogger;


class CampaignController extends Controller
{
    protected $fbAPI;

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

    public function createAd(){
        try {
            Api::init(config('services.facebook.client_id'), config('services.facebook.client_secret'), Auth::user()->access_token);
            $this->fbAPI = Api::instance();
            $this->fbAPI->setLogger(new CurlLogger());
            
            $campaign = new FacebookCampaign(null, 'act_'.Auth::user()->account_id);
            $campaign->setData(array(
                CampaignFields::NAME => "Test Campaign",
                CampaignFields::OBJECTIVE => 'LINK_CLICKS',
                CampaignFields::SPECIAL_AD_CATEGORIES => 'NONE',
                CampaignFields::DAILY_BUDGET => 500 * 100,
                CampaignFields::BUYING_TYPE => 'AUCTION',
                CampaignFields::START_TIME => Carbon::parse('2023-09-21 10:28:43')->timestamp,
            ));

            $campaign->create();
            echo "Campaign ID:".$campaign->id."\n";

            die;

            // $results = TargetingSearch::search(
            //     $type = TargetingSearchTypes::INTEREST,
            //     $class = null,
            //     $query = 'pakistan',
            //   );

            //   // we'll take the top result for now
            //   $target = (count($results)) ? $results->getObjects()[0] : null;
            //   echo "Using target: ".$target->name."\n";

            // $targeting = array(
            //     'geo_locations' => array(
            //       'countries' => array('PK'),
            //     ),
            //     'interests' => array(
            //       array(
            //         'id' => '6003275719927',
            //         'name'=> 'Pakistan',
            //       ),
            //     ),
            //   );

            $adSet = new AdSet(null, 'act_'.Auth::user()->account_id);
            $adSet->setData(array(
                AdSetFields::NAME => 'My Ad Set',
                AdSetFields::OPTIMIZATION_GOAL => AdSetOptimizationGoalValues::REACH,
                AdSetFields::BILLING_EVENT => AdSetBillingEventValues::IMPRESSIONS,
                AdSetFields::BID_AMOUNT => 2,
                AdSetFields::CAMPAIGN_ID => $campaign->id,
                AdSetFields::TARGETING => (new Targeting())->setData(array(
                    TargetingFields::GEO_LOCATIONS => array(
                    'countries' => array('US'),
                    ),
                )),
            ));
            $adSet->create(array(
                AdSet::STATUS_PARAM_NAME => AdSet::STATUS_PAUSED,
            ));

            echo "Ad Set ID:".$adSet->id."\n";


            $image = new AdImage(null, 'act_'.Auth::user()->account_id);
            $image->filename = public_path('laravel.png');;
            

            $image->create();
            echo 'Image Hash: '.$image->hash."\n";

            // Step 3: Create an Ad Creative
            // $adCreative = new AdCreative(null, 'act_'.Auth::user()->account_id);
            // $adCreative->setData(array(
            //     AdCreativeFields::NAME => 'Sample Creative',
            //     AdCreativeFields::TITLE => 'Welcome to the Jungle',
            //     AdCreativeFields::BODY => 'We\'ve got fun \'n\' games',
            //     AdCreativeFields::IMAGE_HASH => $image->hash,
            //     AdCreativeFields::OBJECT_URL => 'http://www.example.com/',
            //   ));
            $adCreative = new AdCreative(null, 'act_'.Auth::user()->account_id);
            $adCreative->setData(array(
                AdCreativeFields::NAME => 'My Ad Creative',
                AdCreativeFields::OBJECT_STORY_SPEC => array(
                    'page_id' => '130939852137532',
                    'link_data' => array(
                        'image_hash' => $image->{AdImageFields::HASH},
                        'link' => 'https://example.com', // URL for the ad destination
                        'message' => 'Your ad message',
                    ),
                ),
            ));

            $adCreative->create();
            

            echo "Ad Creative:".$adCreative->id."\n";


            die;

            // Step 4: Create an Ad
            $ad = new Ad(null, 'act_'.Auth::user()->account_id);
            $ad->setData([
                AdFields::NAME => 'Your Ad Name',
                AdFields::ADSET_ID => $adSet->id,
                AdFields::CREATIVE => [
                    'creative_id' => $adCreative->id,
                ],
                AdFields::STATUS => Ad::STATUS_ACTIVE,
            ]);

            $ad->create();
            echo "Ad ID:".$ad->id."\n";
        } catch (FacebookAdsException $e) {
            $errorData = $e->getResponse()->getContent();

            print_r($errorData); die;
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'page_id' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0.01',
            'target_audience' => 'required|string|max:255',
            'start_date' => 'required|date',
        ]);

        try {
            Api::init(config('services.facebook.client_id'), config('services.facebook.client_secret'), Auth::user()->access_token);
            $this->fbAPI = Api::instance();
            $campaign = new FacebookCampaign(null, 'act_'.Auth::user()->account_id);
            $campaign->setData(array(
                CampaignFields::NAME => $validatedData['name'],
                CampaignFields::OBJECTIVE => 'LINK_CLICKS',
                CampaignFields::SPECIAL_AD_CATEGORIES => 'NONE',
                CampaignFields::DAILY_BUDGET => $validatedData['budget'] * 100,
                CampaignFields::BUYING_TYPE => 'AUCTION',
                CampaignFields::START_TIME => Carbon::parse($validatedData['start_date'])->timestamp,
            ));

            $campaign->create();

            if ($campaign->id)
            {
                $adSet = new AdSet(null, 'act_'.Auth::user()->account_id);
                $adSet->setData(array(
                    AdSetFields::NAME => $validatedData['name']." ADSet",
                    AdSetFields::OPTIMIZATION_GOAL => AdSetOptimizationGoalValues::REACH,
                    AdSetFields::BILLING_EVENT => AdSetBillingEventValues::IMPRESSIONS,
                    AdSetFields::BID_AMOUNT => 2,
                    AdSetFields::CAMPAIGN_ID => $campaign->id,
                    AdSetFields::TARGETING => (new Targeting())->setData(array(
                        TargetingFields::GEO_LOCATIONS => array(
                        'countries' => array($validatedData['target_audience']),
                        ),
                    )),
                ));
                $adSet->create();
            }

            $campaignModel = new Campaign;
            $campaignModel->page_id = $validatedData['page_id'];
            $campaignModel->name = $validatedData['name'];
            $campaignModel->budget = $validatedData['budget'];
            $campaignModel->target_audience = $validatedData['target_audience'];
            $campaignModel->start_date = $validatedData['start_date'];
            $campaignModel->user_id = auth()->user()->id;
            $campaignModel->campaign_id = $campaign->id;
            $campaignModel->save();
            return redirect()->route('campaign.index')->with('success', 'Campaign created successfully.');
        } catch (\Exception $e) {
            echo($e);
            die;
            return redirect()->route('campaign.index')->with('error', 'An error occurred while creating the campaign.<br><br>'.$e);
        }
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
            'start_date' => 'required|date',
        ]);
        
        $campaign->update([
            'page_id' => $validatedData['page_id'],
            'name' => $validatedData['name'],
            'budget' => $validatedData['budget'],
            'target_audience' => $validatedData['target_audience'],
            'start_date' => $validatedData['start_date'],
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
