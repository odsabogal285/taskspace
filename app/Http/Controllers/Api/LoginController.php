<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
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
    private $client, $userRepository;
    public function __construct (UserRepository $userRepository)
    {
        $this->client = Client::where('password_client', true)
                        ->where('revoked', false)
                        ->first();
        $this->userRepository = $userRepository;
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
            $response = Route::dispatch(Request::create('oauth/token', 'POST'));

            if($response->getStatusCode() != 200){
                return response()->json([
                    'status' => "success",
                    'message' => "The credentials incorrect",
                    'data' => null
                ]);
            }

            $accessToken = json_decode((string)$response->content(), true)['access_token'];

            $request->headers->set('Authorization', 'Bearer ' . $accessToken);

            $user= Route::dispatch(Request::create('/api/v1/user-profile', 'GET'));


            if(!$user->content()){
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found',
                    'data' => null
                ], 404);
            }

            $response = json_decode($response->content(), true);
            $response["user"] = json_decode($user->content(), true);

            return response()->json([
                'status' => 'success',
                'message' => 'The credential correct',
                'data' => $response
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

            $user = new User($request->all());
            $user = $this->userRepository->save($user);

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
