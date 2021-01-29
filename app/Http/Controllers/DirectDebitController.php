<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiClientService;
use App\Models\Branch;
use App\Models\Merchant;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Exception;
use DB;
use App\Traits\CustomResponse;

class DirectDebitController extends Controller
{
    use CustomResponse;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function index(Request $request)
    {


        $merchants = Merchant::orderBy('modified', 'desc');
        if ($request->has('transflow_id')) {
            $merchants->where('transflow_id', 'LIKE', "%{$request->transflow_id}%");
        }
        if ($request->has('merchant_name')) {
            $merchants->where('merchant_name', 'LIKE',"%{$request->merchant_name}%");
        }
        if ($request->has('email')) {
            $merchants->where('email', '=', $request->email);
        }
        if ($request->has('phone_number')) {
            $merchants->where('phone_number', '=', $request->phone_number);
        }
        $merchantsData = $merchants->paginate(10);
        if (isset($merchantsData)) {
            return $this->success($merchantsData, 'successfully fetched all merchant', 200);
        } else {
            return $this->error([], 'Failed to retrieve all merchants', 401);
        }
    }


    public function createMerchant(Request $request)
    {
        \Log::debug([$request->all()]);
        $this->validate($request, [
            'transflow_id' => 'required',
            'merchant_name' => 'required',

            'merchant_email' => 'bail|required|email|unique:merchant,email',
            'merchant_phone_number' => 'required'
        ]);
        DB::beginTransaction();

        try {

            $request['email'] = $request->merchant_email;
            $request['phone_number'] = $request->merchant_phone_number;

            $merchant = Merchant::create($request->all());
            $request['merchant_id'] = $merchant->merchant_id;

            \Log::debug([

                'message' => 'Successfully created a merchant',
                'merchant' => $merchant

            ]);

            $branch = $this->setupBranch($request);
            $request['branch_id'] = $branch->branch_id;
            \Log::debug([
                'message' => 'Successfully created a branch',
                'branches' => $branch
            ]);

            $request['email'] = $request->user_email;
            $request['phone_number'] = $request->user_phone_number;
            $request['user_password'] = $this->random_string(10);
            $user =  $this->createUser($request);
            \Log::debug([
                'message' => 'Successfully created a User',
                'user' => $user
            ]);


            $emailParams = [
                'user_password' => $request->user_password,
                'apiKey' => $branch->apiKey,
                'email' => $request->api_key_email,
                'user_email' => $user->email,
                'name' => $user->name,
            ];

            $this->sendApiKey($emailParams);


            DB::commit();

            return response()->json([
                'responseMessage' => "Merchant created successfully", "responseCode" => 200,
                'data' => ['merchant' => $merchant, 'branch' => $branch, 'user' => $user],
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            \Log::debug(['user' => $exception->getMessage()]);
            \Log::error('error storing merchants details', [$exception->getMessage()]);
            return response()->json([
                'responseMessage' => "Integrity constraint violation: Duplicate entry exists", "responseCode" => 400,
            ]);
        }
    }

    public function setupBranch(Request $request)
    {


        //validate the request data

        $this->validate($request, [
            'merchant_id' => 'required',
            'branch_name' => 'required',
            'branch_code' => 'nullable',
            'branch_location' => 'required',
            'branch_email' => 'required',
            'branch_phone_number' => 'required',
            //
        ]);

        //check if merchant is a bank


        //create branch
        $branch = Branch::create($request->all());
        $branch = Branch::where('branch_id', $branch->branch_id)->where('merchant_id', $branch->merchant_id)->with('merchant')->first();

        //
        $branch['apiKey'] = $this->generateApiKey();
        $assignApiKey = $this->assignApiKey($branch);
        return $branch;
    }
    public function createUser(Request $request)
    {
        //get request data
        \Log::debug(['user request values', $request->all()]);


        //validate the request data
        $this->validate($request, [

            'merchant_id' => 'required',
            'branch_id' => 'required',
            'username' => 'required',

            'user_phone_number' => 'required',
            'user_email' => 'required|email|unique:users,email',

        ]);

        // generate random password

        \Log::info(['Successfully Hashing password']);
        $request['password'] = Hash::make($request->user_password);
        $request['name'] = $request->username;
        //create a user
        \Log::debug('Creating in DB');
        \Log::debug('New Request', [$request->all()]);
        $user = User::create($request->all());

        //return a success message to the front
        $userRole = $this->assignUserRole($user);
        return $user;
    }

    public function sendApiKey($emailParams)
    {

        try {
            Mail::to($emailParams['email'])->send(new SendMail($emailParams));
            return true;
        } catch (Exception $exception) {
            \Log::debug(['merchant' => $exception->getMessage()]);
            return false;
        }
    }

    private function generateApiKey()
    {
        //generate random string
        $apiKey = $this->random_string(60);
        //return key
        return $apiKey;
    }

    private function assignApiKey($request)
    {
        //use merchant_id and branch_id to assign ApiKey

        $apiClientDetails = ['client_key' => $request->apiKey, 'merchant_id' => $request->merchant_id, 'client_name' => $request->merchant->merchant_name];
        return ApiClientService::create($apiClientDetails);
    }


    private function assignUserRole($user)
    {
        //set role_id to default value of 4
        $userRoleDetails = ['user_id' => $user->id, 'role_id' => 4];

        $userRole = UserRole::create($userRoleDetails);
        if ($userRole) {
            return response()->json([
                'responseMessage' => "Branch created Successfully", "responseCode" => 200,
                'data' => $userRole,
            ]);
        } else {
            return response()->json([
                'responseMessage' => "Failed", "responseCode" => 401,
            ]);
        }
        //assign user_role

    }

    private function random_string($length)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
    //
}
