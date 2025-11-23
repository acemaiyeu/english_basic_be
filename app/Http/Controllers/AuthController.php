<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Transformers\UserTransformer;

class AuthController extends Controller
{
    // Constructor để áp dụng middleware (trừ login)
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'loginAdmin']]);
    }

    // 1. Đăng nhập và lấy Token
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
    public function loginAdmin()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $role_code = Role::whereNull('deleted_at')->where('id', auth()->user()->role_id)->first()->code;
        if($role_code === 'ADMIN' || $role_code === 'SUPER_ADMIN'){
            return $this->respondWithToken($token);
        }
        return response()->json(['error' => 'Unauthorized - Not Admin'], 401);
    }

    // 2. Lấy thông tin User đang đăng nhập
    public function me()
    {
        
        return fractal(auth()->user(), new UserTransformer())->respond();
    }

    // 3. Đăng xuất (Hủy token)
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // 4. Refresh Token (Lấy token mới thay cho token cũ sắp hết hạn)
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    // Helper function để format dữ liệu trả về
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 // Thời gian hết hạn (giây)
        ]);
    }
}