<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Client;

class LoginController extends Controller
{
    private $client;
    public function __construct ()
    {
        $this->client = Client::where('password_client', true)
                        ->where('revoked', false)
                        ->first();
    }

    public function login (Request $request): JsonResponse
    {
        try {

            $requestData = [
                'grant_type' => 'password',
                'client_id' => $this->client->id,
                'client_secret' => $this->client->secret,
                'username' => $request->input('email'),
                'scope' => '',
            ];

            $request->request->add($requestData);
            $request = Request::create('oauth/token', 'POST');
            $response = Route::dispatch($request);

            if($response->getStatusCode() != 200){
                return response()->json([
                    'status' => "success",
                    'message' => "The credentials incorrect",
                    'data' => null
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'The credential correct',
                'data' => json_decode($response->getContent(), true)
            ]);


        } catch (\Exception $exception) {
            Log::error("Error login LG - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function registerUser (Request $request): JsonResponse
    {
        try {

            DB::beginTransaction();

            $user = User::create([
                'first_name' => $request->input('first_name'),
                'first_surname' => $request->input('first_surname'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password'))
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User created',
                'data' => $user
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("Error: registerUser LG - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
