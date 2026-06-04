<x-guest-layout>
    <div class="mb-6">
        <div class="flex border-b border-gray-200">
            <button id="tabFingerprint" class="px-4 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600 focus:outline-none" onclick="switchTab('fingerprint')">
                Fingerprint Login
            </button>
            <button id="tabPassword" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none" onclick="switchTab('password')">
                Email & Password
            </button>
        </div>
    </div>

    <div id="fingerprintTab">
        <div class="text-center">
            <div id="fpNotSupported" class="hidden bg-yellow-50 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
                This device does not support biometric fingerprint authentication. Please use Email & Password login.
            </div>

            <div id="fpSupported">
                <h3 class="text-lg font-semibold mb-2">Login with Fingerprint</h3>
                <p class="text-sm text-gray-600 mb-6">Use your device's fingerprint sensor to authenticate securely.</p>

                <div id="fingerprintError" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>

                <div class="mb-4">
                    <x-input-label for="fp_email" :value="__('Email')" />
                    <x-text-input id="fp_email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="francis@hesmb.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <button id="fingerprintBtn" onclick="loginWithFingerprint()" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold flex items-center justify-center gap-2" disabled>
                    <span>Checking device...</span>
                </button>

                <div id="trustFingerprint" class="hidden mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700">Fingerprint Verified Login - Your biometric data never leaves your device</p>
                </div>
            </div>
        </div>
    </div>

    <div id="passwordTab" class="hidden">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
                <x-primary-button class="ms-3">{{ __('Log in') }}</x-primary-button>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/webauthn.js') }}"></script>
    <script>
        let webauthnSupported = false;

        async function checkSupport() {
            webauthnSupported = await isWebAuthnSupported();
            const btn = document.getElementById('fingerprintBtn');
            const notSupported = document.getElementById('fpNotSupported');

            if (!webauthnSupported) {
                document.getElementById('fpSupported').classList.add('hidden');
                notSupported.classList.remove('hidden');
                btn.disabled = true;
            } else {
                btn.disabled = false;
                btn.innerHTML = 'Authenticate with Fingerprint';
            }
        }

        function switchTab(tab) {
            const fpTab = document.getElementById('fingerprintTab');
            const pwTab = document.getElementById('passwordTab');
            const fpBtn = document.getElementById('tabFingerprint');
            const pwBtn = document.getElementById('tabPassword');

            if (tab === 'fingerprint') {
                fpTab.classList.remove('hidden');
                pwTab.classList.add('hidden');
                fpBtn.className = 'px-4 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600 focus:outline-none';
                pwBtn.className = 'px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none';
            } else {
                fpTab.classList.add('hidden');
                pwTab.classList.remove('hidden');
                pwBtn.className = 'px-4 py-2 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600 focus:outline-none';
                fpBtn.className = 'px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none';
            }
        }

        async function loginWithFingerprint() {
            const email = document.getElementById('fp_email').value.trim();
            const errorDiv = document.getElementById('fingerprintError');
            const trustDiv = document.getElementById('trustFingerprint');

            errorDiv.classList.add('hidden');
            trustDiv.classList.add('hidden');

            if (!email) {
                errorDiv.textContent = 'Please enter your email address.';
                errorDiv.classList.remove('hidden');
                return;
            }

            const btn = document.getElementById('fingerprintBtn');
            btn.disabled = true;
            btn.innerHTML = '<span>Verifying fingerprint...</span>';

            try {
                const result = await authenticateBiometric(email);
                trustDiv.classList.remove('hidden');
                btn.innerHTML = '<span>Redirecting...</span>';
                window.location.href = result.redirect;
            } catch (err) {
                errorDiv.textContent = err?.message || 'Fingerprint authentication failed';
                errorDiv.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<span>Authenticate with Fingerprint</span>';
            }
        }

        checkSupport();
    </script>
</x-guest-layout>
