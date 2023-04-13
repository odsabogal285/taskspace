<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\user\registerUser;
use App\Models\TaskList;
use App\Models\User;
use App\Repositories\TaskListRepository;
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
    private $client, $userRepository, $taskListRepository;
    public function __construct (UserRepository $userRepository, TaskListRepository $taskListRepository)
    {
        $this->client = Client::where('password_client', true)
                        ->where('revoked', false)
                        ->first();
        $this->userRepository = $userRepository;
        $this->taskListRepository = $taskListRepository;
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

    public function registerUser (registerUser $request): JsonResponse
    {
        try {

            DB::beginTransaction();

            $user = new User($request->all());
            $user = $this->userRepository->save($user);

            $task_list_default = new TaskList(['name' => 'General']);
            $task_list_default = $this->taskListRepository->saveDefault($task_list_default);
            $task_list_default = $this->taskListRepository->syncUsers($task_list_default, array(['id' => $user->id]));

            // $task_list_default->users()->sync($user->id);
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
