<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\OpenAiAuth;
use Illuminate\Http\Request;
use PHPExperts\RESTSpeaker\RESTSpeaker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Models\Country;
use App\Models\GeneralSetting;
use App\Models\EmailLog;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\Banner;
use App\Models\HomePage;
use App\Models\HomePage2Section;
use App\Models\HomePage5Section;
use App\Models\User;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Category;
use App\Models\ListingAd;
use App\Models\ListingAdsImage;
use App\Models\ListingAdDocument;
use App\Models\VideoLibrary;
use App\Models\Enquiry;
use App\Models\UserActivity;
use App\Models\Lead;
use App\Models\State;
use App\Models\Wishlist;
use App\Models\ProductSalesInquiry;
use App\Models\Subscriber;
use App\Models\Source;
use App\Models\Manufacturer;
use App\Models\Model_number;
use App\Models\ReportListingReason;
use App\Models\ListingReportInquiry;
use App\Models\ListingAdsVisit;
use App\Models\ListingAdsVideo;
use App\Models\ListingAdRequest;

use Auth;
use Session;
use Helper;
use Hash;
use stripe;

class FrontController extends Controller
{
    public function home(){
        return redirect(url('/admin'));
    }  
}
