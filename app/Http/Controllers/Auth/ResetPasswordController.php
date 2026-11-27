<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Quy tắc kiểm tra: mật khẩu tối thiểu 6 ký tự (khớp với trang đăng ký).
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    /**
     * Đặt lại mật khẩu nhưng KHÔNG tự đăng nhập: người dùng phải đăng nhập lại bằng mật khẩu mới.
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));
    }

    /**
     * Sau khi đặt lại thành công, chuyển về trang đăng nhập kèm thông báo.
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect()->route('login')->with('status', trans($response));
    }
}
