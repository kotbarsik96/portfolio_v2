<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|unique:users,name',
            'email' => 'required|string:unique:users,name',
            'telegram' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'kotbarsik96_portfolio: ' . $fields['name'],
            '',
            $secretKey
        );
        $google2fa_url = QrCode::generate($qrCodeUrl);

        $user = User::create([
            'name' => e($fields['name']),
            'email' => e(filter_var($fields['email'], FILTER_SANITIZE_EMAIL)),
            'telegram' => e($fields['telegram']),
            'password' => bcrypt($fields['password']),
            'google2fa_secret' => $secretKey
        ]);

        return response(['user' => $user, 'auth_qr_code' => $google2fa_url->toHtml()]);
    }

    public function checkAuth(Request $request)
    {
        
    }

    public function validateLogin(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('name', e($fields['name']))->first();
        if (empty($user) || !Hash::check($fields['password'], $user->password))
            return false;

        return ['user' => $user, 'fields' => $fields];
    }

    public function login(Request $request)
    {
        $validation = $this->validateLogin($request);
        if (!$validation)
            return response(['success' => false]);

        return response(['success' => true]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response(['success' => true]);
    }

    public function validate2fa(Request $request)
    {
        $validation = $this->validateLogin($request);
        if (!$validation)
            return response(['success' => false]);

        $user = $validation['user'];
        $fields = $validation['fields'];
        Auth::attempt($fields, true);

        $google2fa = new Google2FA();
        $isValid = $google2fa->verifyKey($user->google2fa_secret, $request['secret']);

        if (!$isValid)
            return response(['success' => false]);

        return [
            'user' => $user,
            'success' => true
        ];
    }
}