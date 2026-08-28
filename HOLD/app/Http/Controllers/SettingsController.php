<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Generalsetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Services\Permissions\PermissionHelperService;
class SettingsController extends Controller 
{
    private $module = 'CAR_FARE_MODULE';
    private $permission;
    public function __construct()
    {
        $this->permission = new PermissionHelperService;
    }
    public function index()
    {
        if (session('redirect_url') != '/general') {
            return redirect(session('redirect_url'));
        } else {
            //  dd('king');
            $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
            //UI permissions array destructured
            [
                'CREATE' => $IS_CREATABLE,
                'UPDATE' => $IS_UPDATABLE,
                'DELETE' => $IS_DELETABLE
            ] = $this->permission->ui_permissions($this->module);
            $list_car_fares = DB::table('gentral_setting')->select('*')->get();
            // Pass responseMessage to the view
            return view('generalsettings.index', compact('list_car_fares'));
        }
    }
    
    public function faretype(){
        return view('bookingsetting.faretype');
    }
    
    public function checkDriverVehicle()
    {
        $data = DB::table('vehicle')->exists();
        $datas = DB::table('driver')->exists();
        if ($data && !$datas) {
            return response()->json(['status' => 'error', 'message' => 'Please create a vehicle and a driver first.']);
        } elseif (!$data) {
            return response()->json(['status' => 'error', 'message' => 'Please create a vehicle first.']);
        } elseif (!$datas) {
            return response()->json(['status' => 'error', 'message' => 'Please create a driver first.']);
        }
        return response()->json(['status' => 'success', 'message' => 'Data exists.']);
    }
    public function bookingsetting()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        $list_car_fares = DB::table('gentral_setting')->select('*')->get();
        // dd($list_car_fares);
        return view('bookingsetting.index', compact('list_car_fares', 'IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function articleset(Request $request)
    {
        $id = $request->id ?? '';
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        $list_car_fares = DB::table('gentral_setting')->select('*')->get();
        return view('bookingsetting.articleset', compact('list_car_fares', 'IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE', 'id'));
    }
    public function firstBookingSetting()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        $list_car_fares = DB::table('gentral_setting')->select('*')->get();
        // dd($list_car_fares);
        return view('bookingsetting.firstBooking', compact('list_car_fares', 'IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function emailsetting()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('emailsetting.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function firstEmailsetting()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('emailsetting.firstEmail', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function paymentoption()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('paymentsetting.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function whatsapp_configuration()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('whatsappsetting.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    
    public function call_configuration()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('twilioSetting.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }

     public function reviewdetails()
    {
        return view('review.reviewdetails');
    }
    
     public function tariff()
    {
        return view('car_rent.create');
    }
    
    public function firstPaymentoption()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('paymentsetting.firstPayment', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }

     public function starrating(Request $request)
        {
            // return $request->all();
            try {
                $encrypted = $request->query('data');
                $decoded = base64_decode(urldecode($encrypted));
                
                // Split back into individual values
                // dd($decoded); 
                [$name, $jobNo, $email, $token, $weburl] = explode('|', $decoded);
                return view('review.partials.starrating', compact('name', 'jobNo', 'email', 'token', 'weburl'));
                
            } catch (\Exception $e) {
                $encrypted = $request->query('data');
                dd($e->getMessage(), $encrypted);
                return abort(403, 'Invalid or expired link.');
            }
        }
    // public function starrating(){
    //      return view('review.partials.starrating');
    // }
    public function bookingrestriction()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('bookingrestriction.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function googlecallender()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('googlecalendar.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function review()
    {
        // dd('king');
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('review.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function page()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('page.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
    public function templaterequest()
    {
        $this->permission->check_privilege($this->module, self::ACTION_TYPE['index']);
        //UI permissions array destructured
        [
            'CREATE' => $IS_CREATABLE,
            'UPDATE' => $IS_UPDATABLE,
            'DELETE' => $IS_DELETABLE
        ] = $this->permission->ui_permissions($this->module);
        // dd($list_car_fares);
        return view('page.index', compact('IS_UPDATABLE', 'IS_CREATABLE', 'IS_DELETABLE'));
    }
}
