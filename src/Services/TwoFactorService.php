<?php


namespace Lara\Jarvis\Services;

use Illuminate\Http\Request;
use Lara\Jarvis\Http\Resources\DefaultCollection;
use Lara\Jarvis\Validators\Twofactor\TwoFactorDisableValidator;
use Lara\Jarvis\Validators\Twofactor\TwoFactorEnableValidator;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\DB;

class TwoFactorService
{
    private $google2fa;

    public function __construct ()
    {
        $this->google2fa = new Google2FA ();
    }

    public function resourceCollection ()
    {
        return DefaultCollection::class;
    }

    public function getUrlCode (Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_enable) {

            $secretKey = $this->google2fa->generateSecretKey();

            DB::transaction(function () use ($request, &$user, $secretKey) {
                $request->user()->update([
                    'secret_key' => $secretKey
                ]);
            });

            $google2fa_url = $this->google2fa->getQRCodeUrl(
                "Muito.io",
                $user->email,
                $secretKey
            );

            return $google2fa_url;
        }

        return response()->json(['message' => 'Autenticação já habilitada.'], 422);
    }

    public function enable (Request $request)
    {
        $user = $request->user();

        if (!$user->two_factor_enable) {

            TwoFactorEnableValidator::validate($request->all());

            $valid = $this->google2fa->verifyKey($user->secret_key, $request->secret);
            if ($valid) {
                DB::transaction(function () use ($request, &$user) {
                    $user = $request->user()->update([
                        'two_factor_enable' => true,
                    ]);
                });

                return response()->json(null, 200);

            } else {
                return response()->json(['message' => 'Código Inválido'], 422);
            }
        } else {
            return response()->json(['message' => 'Autenticação já habilitada.'], 422);
        }
    }

    public function disable (Request $request)
    {

        TwoFactorDisableValidator::validate($request->all());

        $user = DB::table('users')->find($request->user_id);

        if ($user->two_factor_enable) {

            DB::transaction(function () use ($request, &$user) {

                DB::table('users')->where('id', $request->user_id)->update([
                    'two_factor_enable' => false,
                ]);
            });

            return response()->json(null, 200);

        } else {
            return response()->json(['message' => 'Autenticação já desabilitada.'], 422);
        }
    }

    public function check (Request $request)
    {
        if ($request->user()->two_factor_enable) {
            return response()->json(null, 200);

        } else {
            return response()->json(['message' => 'Autenticação não está habilitada.'], 422);
        }
    }
}
