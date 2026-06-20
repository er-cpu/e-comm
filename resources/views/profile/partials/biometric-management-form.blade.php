<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Biometric Fingerprint') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Manage your fingerprint credentials for secure biometric login.') }}
        </p>
    </header>

    <div class="mt-6">
        <div id="biometricStatus" class="hidden mb-4 p-3 rounded text-sm"></div>

        <div id="bioCredsList" class="space-y-3">
            <div class="text-sm text-gray-500 italic">Loading credentials...</div>
        </div>

        <div id="bioNoCreds" class="hidden">
            <p class="text-sm text-gray-500">No fingerprint credentials registered.</p>
        </div>

        <div id="bioCredsError" class="hidden mb-3 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700"></div>
        <div id="bioCredsSuccess" class="hidden mb-3 p-3 bg-green-50 border border-green-200 rounded text-sm text-green-700"></div>

        <div class="mt-4 flex items-center gap-4">
            <button type="button" id="enrollBioBtn" onclick="profileEnrollFingerprint()" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                Register New Fingerprint
            </button>
            <div id="bioEnrollSpinner" class="hidden text-sm text-gray-500">Scanning fingerprint...</div>
        </div>

        <div id="bioEnrollError" class="hidden mt-2 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-700"></div>
        <div id="bioEnrollSuccess" class="hidden mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm text-green-700">Fingerprint enrolled successfully!</div>

        <div id="bioOtpFallback" class="hidden mt-4 p-4 bg-white border border-gray-200 rounded-lg">
            <p class="text-sm font-medium text-gray-700 mb-2">Fingerprint setup unavailable on this device.</p>
            <p class="text-xs text-gray-500 mb-3">Verify your phone number with an OTP as backup authentication.</p>

            <div class="flex gap-2 mb-3">
                <input type="text" id="otpPhoneInput" placeholder="+255 700 600 500"
                       class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                       value="{{ Auth::user()->phone ?? '' }}">
                <button type="button" id="sendOtpBtn" onclick="sendOtp()"
                        class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 whitespace-nowrap">
                    Send OTP
                </button>
            </div>

            <div class="flex gap-2 mb-3 hidden" id="otpVerifyRow">
                <input type="text" id="otpCodeInput" placeholder="Enter 6-digit OTP"
                       class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500"
                       maxlength="6">
                <button type="button" id="verifyOtpBtn" onclick="verifyOtp()"
                        class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 whitespace-nowrap">
                    Verify OTP
                </button>
            </div>

            <div id="otpDebugCode" class="text-amber-700 hidden text-sm mt-1 mb-2"></div>
            <div id="otpStatus" class="text-sm hidden"></div>
            <div id="otpSuccess" class="hidden text-sm text-green-600">Phone verified successfully! You can now use OTP as a backup authentication method.</div>
        </div>
    </div>

    <script src="{{ asset('js/webauthn.js') }}"></script>
    <script>
        function safeErrorMsg(err, fallback) {
            return (err && err.message) ? err.message : (typeof err === 'string' ? err : fallback);
        }

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

        async function sendOtp() {
            const phone = document.getElementById('otpPhoneInput').value.trim().replace(/\s+/g, '');
            const btn = document.getElementById('sendOtpBtn');
            const status = document.getElementById('otpStatus');
            status.classList.add('hidden');

            if (!phone.match(/^(0|\+255)\d{9}$/)) {
                showOtpStatus('Invalid phone number. Use format: +255 700 600 500', 'error');
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

                var debugEl = document.getElementById('otpDebugCode');
                if (data.debug_code) {
                    debugEl.textContent = 'Debug OTP: ' + data.debug_code;
                    debugEl.classList.remove('hidden');
                } else {
                    debugEl.classList.add('hidden');
                }
            } catch (err) {
                showOtpStatus(safeErrorMsg(err, 'Failed to send OTP'), 'error');
                btn.textContent = 'Send OTP';
                btn.disabled = false;
            }
        }

        async function verifyOtp() {
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
                showOtpStatus(safeErrorMsg(err, 'Verification failed'), 'error');
                btn.disabled = false;
                btn.textContent = 'Verify OTP';
            }
        }

        async function loadCredentials() {
            const listEl = document.getElementById('bioCredsList');
            const noCredsEl = document.getElementById('bioNoCreds');
            const errorEl = document.getElementById('bioCredsError');

            try {
                const response = await fetch('{{ route("biometric.credentials") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();

                if (!response.ok) throw new Error(data.error || 'Failed to load credentials');

                if (data.credentials.length === 0) {
                    listEl.classList.add('hidden');
                    noCredsEl.classList.remove('hidden');
                } else {
                    listEl.classList.remove('hidden');
                    noCredsEl.classList.add('hidden');
                    listEl.innerHTML = data.credentials.map(cred => `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Fingerprint credential</span>
                                <span class="text-xs text-gray-500 ml-2">Registered ${cred.created_at}</span>
                            </div>
                            <button onclick="deleteCredential(${cred.id})" class="text-red-600 text-sm hover:underline">Remove</button>
                        </div>
                    `).join('');
                }
            } catch (err) {
                errorEl.textContent = safeErrorMsg(err, 'Failed to load credentials');
                errorEl.classList.remove('hidden');
            }
        }

        async function deleteCredential(id) {
            if (!confirm('Remove this fingerprint credential?')) return;

            const errorEl = document.getElementById('bioCredsError');
            const successEl = document.getElementById('bioCredsSuccess');
            errorEl.classList.add('hidden');
            successEl.classList.add('hidden');

            try {
                const token = document.querySelector('meta[name="csrf-token"]');
                const response = await fetch('{{ url("/biometric/credentials") }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token ? token.content : '',
                    }
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Failed to delete');

                successEl.textContent = 'Fingerprint credential removed.';
                successEl.classList.remove('hidden');
                loadCredentials();
            } catch (err) {
                errorEl.textContent = safeErrorMsg(err, 'Failed to delete credential');
                errorEl.classList.remove('hidden');
            }
        }

        async function profileEnrollFingerprint() {
            const btn = document.getElementById('enrollBioBtn');
            const spinner = document.getElementById('bioEnrollSpinner');
            const errorEl = document.getElementById('bioEnrollError');
            const successEl = document.getElementById('bioEnrollSuccess');

            btn.disabled = true;
            btn.classList.add('hidden');
            spinner.classList.remove('hidden');
            errorEl.classList.add('hidden');
            successEl.classList.add('hidden');

            try {
                await registerBiometric({{ Auth::user()->id }});
                successEl.classList.remove('hidden');
                spinner.classList.add('hidden');
                btn.classList.remove('hidden');
                btn.disabled = false;
                loadCredentials();
            } catch (err) {
                document.getElementById('bioOtpFallback').classList.remove('hidden');
                errorEl.textContent = safeErrorMsg(err, 'Fingerprint enrollment failed');
                errorEl.classList.remove('hidden');
                spinner.classList.add('hidden');
                btn.classList.remove('hidden');
                btn.disabled = false;
            }
        }

        loadCredentials();
    </script>
</section>
