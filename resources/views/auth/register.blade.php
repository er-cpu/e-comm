<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <div class="flex gap-4">
            <div class="w-1/2">
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus autocomplete="given-name" />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>
            <div class="w-1/2">
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required autocomplete="family-name" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone Number (optional)')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="0689045666 or +255689045666" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            <p class="text-xs text-gray-500 mt-1">Used as backup if fingerprint is unavailable.</p>
        </div>

        <div id="biometricSection" class="mt-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg hidden">
            <h3 class="font-semibold text-indigo-800">Enroll Fingerprint</h3>
            <p class="text-sm text-indigo-600 mt-1">Register your fingerprint for secure biometric login.</p>
            <div id="bioNotSupported" class="hidden mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">
                Your device does not support fingerprint enrollment. You can still login with email & password.
            </div>
            <div id="bioError" class="hidden mt-2 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-700"></div>
            <div id="bioSuccess" class="hidden mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                Fingerprint enrolled successfully!
            </div>
            <div id="bioOtpFallback" class="hidden mt-4 p-4 bg-white border border-gray-200 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Fingerprint setup unavailable on this device.</p>
                <p class="text-xs text-gray-500 mb-3">Verify your phone number with an OTP as backup authentication.</p>

                <div class="flex gap-2 mb-3">
                    <input type="text" id="otpPhoneInput" placeholder="0689045666 or +255689045666"
                           class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="button" id="sendOtpBtn" onclick="sendRegOtp()"
                            class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 whitespace-nowrap">
                        Send OTP
                    </button>
                </div>

                <div class="flex gap-2 mb-3 hidden" id="otpVerifyRow">
                    <input type="text" id="otpCodeInput" placeholder="Enter 6-digit OTP"
                           class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                           maxlength="6">
                    <button type="button" id="verifyOtpBtn" onclick="verifyRegOtp()"
                            class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 whitespace-nowrap">
                        Verify OTP
                    </button>
                </div>

                <div id="otpStatus" class="text-sm hidden"></div>
                <div id="otpSuccess" class="hidden text-sm text-green-600">Phone verified successfully!</div>
            </div>
            <button type="button" id="enrollBtn" onclick="enrollFingerprint()" class="mt-3 bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700" disabled>
                Checking device...
            </button>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">{{ __('Register') }}</x-primary-button>
        </div>
    </form>

    <script src="{{ asset('js/webauthn.js') }}"></script>
    <script>
        function csrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        }

        function showOtpStatus(msg, type) {
            const el = document.getElementById('otpStatus');
            el.textContent = msg;
            el.className = 'text-sm p-2 rounded ' + (type === 'error' ? 'text-red-700 bg-red-50' : 'text-green-700 bg-green-50');
            el.classList.remove('hidden');
        }

        async function sendRegOtp() {
            const phone = document.getElementById('otpPhoneInput').value.trim();
            const btn = document.getElementById('sendOtpBtn');
            const status = document.getElementById('otpStatus');
            status.classList.add('hidden');

            if (!phone.match(/^(0|\+255)\d{9}$/)) {
                showOtpStatus('Invalid phone number. Use format: 0689045666 or +255689045666', 'error');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Sending...';

            try {
                const res = await fetch('{{ route("otp.send") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                    body: JSON.stringify({ phone }),
                });
                const data = await res.json();

                if (!res.ok) throw new Error(data.error || 'Failed to send OTP');

                showOtpStatus(data.message, 'success');
                document.getElementById('otpVerifyRow').classList.remove('hidden');
                btn.textContent = 'Resend OTP';
                btn.disabled = false;
            } catch (err) {
                showOtpStatus(err?.message || 'Failed to send OTP', 'error');
                btn.textContent = 'Send OTP';
                btn.disabled = false;
            }
        }

        async function verifyRegOtp() {
            const code = document.getElementById('otpCodeInput').value.trim();
            const btn = document.getElementById('verifyOtpBtn');
            const status = document.getElementById('otpStatus');
            const success = document.getElementById('otpSuccess');
            status.classList.add('hidden');
            success.classList.add('hidden');

            if (code.length !== 6 || !/^\d{6}$/.test(code)) {
                showOtpStatus('Enter a valid 6-digit OTP code', 'error');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Verifying...';

            try {
                const res = await fetch('{{ route("otp.verify") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                    body: JSON.stringify({ code }),
                });
                const data = await res.json();

                if (!res.ok) throw new Error(data.error || 'Verification failed');

                success.classList.remove('hidden');
                document.getElementById('otpVerifyRow').classList.add('hidden');
                document.getElementById('sendOtpBtn').disabled = true;
                btn.textContent = 'Verified';
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
            } catch (err) {
                showOtpStatus(err?.message || 'Verification failed', 'error');
                btn.disabled = false;
                btn.textContent = 'Verify OTP';
            }
        }

        let bioUserId = null;

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = 'Registering...';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (!response.ok) {
                    let errors = data.errors || {};
                    let errorMsg = 'Registration failed.';
                    for (let key in errors) {
                        errorMsg += '\n' + errors[key].join(', ');
                    }
                    alert(errorMsg);
                    btn.disabled = false;
                    btn.innerHTML = 'Register';
                    return;
                }

                bioUserId = data.user_id;
                document.getElementById('biometricSection').classList.remove('hidden');

                const supported = await isWebAuthnSupported();
                const enrollBtn = document.getElementById('enrollBtn');

                if (supported) {
                    enrollBtn.disabled = false;
                    enrollBtn.innerHTML = 'Enroll Fingerprint Now';
                } else {
                    document.getElementById('bioNotSupported').classList.remove('hidden');
                    document.getElementById('bioOtpFallback').classList.remove('hidden');
                    enrollBtn.disabled = true;
                    enrollBtn.innerHTML = 'Not available';
                    setTimeout(() => window.location.href = data.redirect, 2000);
                }
            } catch (err) {
                alert('Registration error: ' + (err?.message || 'An unexpected error occurred'));
                btn.disabled = false;
                btn.innerHTML = 'Register';
            }
        });

        async function enrollFingerprint() {
            const btn = document.getElementById('enrollBtn');
            const errorDiv = document.getElementById('bioError');
            const successDiv = document.getElementById('bioSuccess');

            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
            btn.disabled = true;
            btn.innerHTML = 'Scanning fingerprint...';

            try {
                await registerBiometric(bioUserId);
                successDiv.classList.remove('hidden');
                btn.innerHTML = 'Enrolled!';
                setTimeout(() => window.location.href = '{{ route("dashboard") }}', 1500);
            } catch (err) {
                document.getElementById('bioOtpFallback').classList.remove('hidden');
                errorDiv.textContent = err?.message || 'Fingerprint enrollment failed';
                errorDiv.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = 'Try Again';
            }
        }
    </script>
</x-guest-layout>
