<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Transformers\UserTransformer;
use App\Models\UserModel;

class AuthController extends Controller
{
    // Constructor để áp dụng middleware (trừ login)
    protected  $UserModel;
    public function __construct(UserModel $model)
    {
        $this->UserModel = $model;
        $this->middleware('auth:api', ['except' => ['login', 'loginAdmin', 'register']]);
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
    public function register()
    {
        $credentials = request(['email', 'password', 'name']);
        $check = \App\Models\User::where('email', $credentials['email'])->first();
        if($check){
            return response()->json(['error' => 'Email already exists'], 400);
        }
        $user = new \App\Models\User();
        $user->email = $credentials['email'];
        $user->password = bcrypt($credentials['password']);
        $user->name =  $credentials['name'];
        $user->role_id = Role::whereNull('deleted_at')->where('code', 'GUEST')->first()->id;
        $user->save();

        return $this->login();
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
    public function update(Request $req)
    {
        $user = $this->UserModel->update($req, auth()->user()->id);
        return fractal($user, new UserTransformer())->respond();
    }
}