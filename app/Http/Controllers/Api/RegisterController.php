<?php

namespace App\Http\Controllers\API;

use App\Models\Store;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function registerVendor(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'store_name' => 'required',
            'description' => 'required',
            'lat' => 'required|decimal:0,9',
            'long' => 'required|decimal:0,9',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = [
            'name' => $request->name,
            'role_id' => 1,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ];
        $user = User::create($input);
        $store_data = [
            'name' => $request->store_name,
            'description' => $request->description,
            'lat' => $request->lat,
            'long' => $request->long,
            'admin_id' => $user->id,
        ];
        $store = Store::create($store_data);
        Wallet::create(['store_id' => $store->id]);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['vendor'] = User::with(['store', 'wallet'])->find($user->id);;

        return $this->sendResponse($success, 'Vendor register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['user'] = $user;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'role_id' => 'required',
            'store_id' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = new User();
        $user->name = $request->name;
        $user->role_id = $request->role_id;
        $user->store_id = $request->store_id;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        Wallet::create(['user_id' => $user->id]);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['customer'] = $user;
        return $this->sendResponse($success, 'user register successfully.');

    }
}
