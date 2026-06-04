<?php

namespace App\Http\Controllers;

use App\Models\BiometricSetupToken;
use App\Models\Credential;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use lbuchs\WebAuthn\WebAuthn;

class BiometricController extends Controller
{
    protected function webauthn(Request $request)
    {
        $host = $request->getHost();
        if (str_contains($host, ':')) {
            $host = explode(':', $host)[0];
        }
        // Map 127.0.0.1 to localhost for WebAuthn origin checking
        if ($host === '127.0.0.1') {
            $host = 'localhost';
        }
        return new WebAuthn(config('app.name'), $host);
    }

    protected function getChallenge($obj)
    {
        if ($obj === null) return null;
        if (is_string($obj)) return $obj;
        if (is_object($obj) && method_exists($obj, 'getBinaryString')) {
            return $obj->getBinaryString();
        }
        return (string) $obj;
    }

    public function registerOptions(Request $request)
    {
        $user = $request->user() ?? User::find($request->user_id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $webauthn = $this->webauthn($request);
        $userId = hash('sha256', (string) $user->id, true);
        $args = $webauthn->getCreateArgs($userId, $user->email, $user->first_name . ' ' . $user->last_name, 20);

        $challenge = $this->getChallenge($args->publicKey->challenge);
        session(['webauthn_challenge' => base64_encode($challenge)]);

        $args->publicKey->challenge = base64_encode($challenge);
        $args->publicKey->user->id = base64_encode($args->publicKey->user->id);

        return response()->json($args->publicKey);
    }

    public function register(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'credential' => 'required|array',
            'credential.response.clientDataJSON' => 'required|string',
            'credential.response.attestationObject' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);
        $challenge = base64_decode(session('webauthn_challenge'));
        if (!$challenge) {
            return response()->json(['error' => 'Challenge expired, please try again'], 400);
        }
        session()->forget('webauthn_challenge');

        try {
            $webauthn = $this->webauthn($request);
            $clientDataJSON = base64_decode($request->input('credential.response.clientDataJSON'));
            $attestationObject = base64_decode($request->input('credential.response.attestationObject'));

            $credentialData = $webauthn->processCreate(
                $clientDataJSON,
                $attestationObject,
                $challenge
            );

            Credential::create([
                'user_id' => $user->id,
                'credential_id' => base64_encode($credentialData->credentialId),
                'public_key' => base64_encode($credentialData->credentialPublicKey),
                'sign_count' => $credentialData->signatureCounter ?? 0,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Biometric registration failed: ' . $e->getMessage()], 400);
        }
    }

    public function authenticateOptions(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $credentials = Credential::where('user_id', $user->id)->get();
        if ($credentials->isEmpty()) {
            return response()->json(['error' => 'No biometric credentials registered. Please use Email & Password login.'], 404);
        }

        $allowedCredentials = $credentials->map(function ($cred) {
            return base64_decode($cred->credential_id);
        })->toArray();

        $webauthn = $this->webauthn($request);
        $args = $webauthn->getGetArgs($allowedCredentials, 20);

        $challenge = $this->getChallenge($args->publicKey->challenge);
        session([
            'webauthn_auth_challenge' => base64_encode($challenge),
            'webauthn_auth_user_id' => $user->id,
        ]);

        $args->publicKey->challenge = base64_encode($challenge);

        if (isset($args->publicKey->allowCredentials)) {
            foreach ($args->publicKey->allowCredentials as $cred) {
                $cred->id = base64_encode($cred->id->getBinaryString());
            }
        }

        return response()->json($args->publicKey);
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'credential' => 'required|array',
            'credential.response.clientDataJSON' => 'required|string',
            'credential.response.authenticatorData' => 'required|string',
            'credential.response.signature' => 'required|string',
        ]);

        $challenge = base64_decode(session('webauthn_auth_challenge'));
        $userId = session('webauthn_auth_user_id');

        if (!$challenge || !$userId) {
            return response()->json(['error' => 'Session expired, please try again'], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $credential = Credential::where('user_id', $user->id)->first();
        if (!$credential) {
            return response()->json(['error' => 'Credential not found'], 404);
        }

        session()->forget(['webauthn_auth_challenge', 'webauthn_auth_user_id']);

        try {
            $webauthn = $this->webauthn($request);

            $clientDataJSON = base64_decode($request->input('credential.response.clientDataJSON'));
            $authenticatorData = base64_decode($request->input('credential.response.authenticatorData'));
            $signature = base64_decode($request->input('credential.response.signature'));
            $credentialPublicKey = base64_decode($credential->public_key);

            $webauthn->processGet(
                $clientDataJSON,
                $authenticatorData,
                $signature,
                $credentialPublicKey,
                $challenge,
                $credential->sign_count
            );

            $newSignCount = $webauthn->getSignatureCounter();
            if ($newSignCount !== null) {
                $credential->update(['sign_count' => $newSignCount]);
            }
            Auth::login($user);

            return response()->json(['success' => true, 'redirect' => route('dashboard')]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed: ' . $e->getMessage()], 400);
        }
    }

    public function credentials(Request $request)
    {
        $credentials = Credential::where('user_id', $request->user()->id)->get()->map(function ($cred) {
            return [
                'id' => $cred->id,
                'created_at' => $cred->created_at->format('M d, Y'),
            ];
        });

        return response()->json(['credentials' => $credentials]);
    }

    public function destroyCredential(Request $request, Credential $credential)
    {
        if ($credential->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $credential->delete();

        return response()->json(['success' => true]);
    }

    public function generateSetupToken(Request $request)
    {
        $token = Str::random(64);
        BiometricSetupToken::create([
            'user_id' => $request->user()->id,
            'token' => $token,
            'expires_at' => now()->addHours(2),
        ]);

        // Use the actual request host so the QR code URL works from other devices on the network
        $base = $request->getSchemeAndHttpHost();
        $url = $base . '/biometric/setup/' . $token;

        return response()->json([
            'token' => $token,
            'url' => $url,
        ]);
    }

    public function setupForm($token)
    {
        $record = BiometricSetupToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response('This setup link has expired or is invalid. Please request a new one.', 404);
        }

        $user = $record->user;

        return view('biometric.setup', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function setupRegister(Request $request, $token)
    {
        $record = BiometricSetupToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Setup link expired or invalid'], 400);
        }

        $user = $record->user;

        $request->validate([
            'credential' => 'required|array',
            'credential.response.clientDataJSON' => 'required|string',
            'credential.response.attestationObject' => 'required|string',
        ]);

        $challenge = base64_decode(session('webauthn_challenge'));
        if (!$challenge) {
            return response()->json(['error' => 'Challenge expired, please try again'], 400);
        }

        $webauthn = $this->webauthn($request);

        try {
            $clientDataJSON = base64_decode($request->input('credential.response.clientDataJSON'));
            $attestationObject = base64_decode($request->input('credential.response.attestationObject'));
            $credentialData = $webauthn->processCreate($clientDataJSON, $attestationObject, $challenge);

            Credential::create([
                'user_id' => $user->id,
                'credential_id' => base64_encode($credentialData->credentialId),
                'public_key' => base64_encode($credentialData->credentialPublicKey),
                'sign_count' => $credentialData->signatureCounter ?? 0,
            ]);

            $record->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Biometric registration failed: ' . $e->getMessage()], 400);
        }
    }

    public function checkDeviceSupport(Request $request)
    {
        return response()->json([
            'supported' => true,
            'message' => 'WebAuthn is available on this device',
        ]);
    }
}
