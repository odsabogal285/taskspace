<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function profile (Request $request): ?User
    {
        try {
            return $this->userRepository->get(auth()->id());
        } catch (\Exception $exception) {
            Log::error("Error profile UC - API, message: {$exception->getMessage()}, file: {$exception->getFile()}, line: {$exception->getLine()}");
            return null;
        }
    }
}
