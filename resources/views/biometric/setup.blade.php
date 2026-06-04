<x-guest-layout>
    <div class="text-center">
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Biometric Fingerprint Setup</h2>
        <p class="text-sm text-gray-600 mb-6">
            Register your fingerprint for account: <strong>{{ $user->email }}</strong>
        </p>

        <div id="setupStatus" class="hidden mb-4 p-3 rounded text-sm"></div>

        <div id="setupNotSupported" class="hidden bg-yellow-50 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
            This device does not support biometric fingerprint authentication.
        </div>

        <div id="setupSupported">
            <button type="button" id="setupEnrollBtn" onclick="startSetup()" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                Register Fingerprint
            </button>
        </div>

        <div id="setupSuccess" class="hidden mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700">Fingerprint enrolled successfully! You can now close this page and log in with your fingerprint.</p>
        </div>
    </div>

    <script src="{{ asset('js/webauthn.js') }}"></script>
    <script>
        async function startSetup() {
            const btn = document.getElementById('setupEnrollBtn');
            const status = document.getElementById('setupStatus');
            const success = document.getElementById('setupSuccess');

            status.classList.add('hidden');
            success.classList.add('hidden');
            btn.disabled = true;
            btn.textContent = 'Scanning fingerprint...';

            const supported = await isWebAuthnSupported();
            if (!supported) {
                document.getElementById('setupNotSupported').classList.remove('hidden');
                document.getElementById('setupSupported').classList.add('hidden');
                btn.disabled = false;
                btn.textContent = 'Register Fingerprint';
                return;
            }

            try {
                const optResponse = await fetch('{{ route("biometric.register.options") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ user_id: {{ $user->id }} }),
                });

                if (!optResponse.ok) {
                    let errMsg;
                    try { const d = await optResponse.json(); errMsg = d.error; } catch (e) {}
                    throw new Error(errMsg || 'Failed to get registration options');
                }

                const options = await optResponse.json();
                if (!options || !options.challenge) {
                    throw new Error('Invalid registration options from server');
                }

                const publicKey = encodeServerOptions(options);
                const credential = await navigator.credentials.create({ publicKey });
                const decoded = decodeCredentialResponse(credential);

                const verifyResponse = await fetch('{{ route("biometric.setup.register", ["token" => $token]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ credential: decoded }),
                });

                const result = await verifyResponse.json();

                if (!verifyResponse.ok) {
                    throw new Error(result.error || 'Registration failed');
                }

                success.classList.remove('hidden');
                btn.textContent = 'Enrolled';
            } catch (err) {
                status.textContent = err && err.message ? err.message : (typeof err === 'string' ? err : 'Fingerprint enrollment failed. This device may not support biometric registration over HTTP.');
                status.className = 'mb-4 p-3 rounded text-sm bg-red-50 border border-red-200 text-red-700';
                status.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = 'Try Again';
            }
        }
    </script>
</x-guest-layout>
