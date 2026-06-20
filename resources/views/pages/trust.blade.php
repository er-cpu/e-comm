<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Trust Policy</h2>
    </x-slot>
    <div class="py-12" style="background: linear-gradient(135deg, #ecfeff 0%, #f8fafc 45%, #f0fdfa 100%);">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8 border border-cyan-100">
                <div class="mb-8">
                    
                    <h1 class="text-3xl font-bold mb-4 text-slate-900">Trust Policy</h1>

                    <p class="text-gray-700 leading-relaxed">
                        We design every shopping session to feel safe, clear, and dependable. Our trust system checks account activity,
                        device signals, payment behavior, and login security so we can protect customers while keeping the buying
                        experience simple.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="rounded-lg border border-teal-100 bg-teal-50 p-4">
                        <h3 class="font-semibold text-slate-900 mb-2">Secure Sign-In</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Fingerprint login, OTP backup, and password checks help confirm that the real account owner is accessing the account.
                        </p>
                    </div>
                    <div class="rounded-lg border border-cyan-100 bg-cyan-50 p-4">
                        <h3 class="font-semibold text-slate-900 mb-2">Safer Devices</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Devices that pass security checks can be treated as familiar, while unusual access attempts receive extra attention.
                        </p>
                    </div>
                    <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-4">
                        <h3 class="font-semibold text-slate-900 mb-2">Protected Payments</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Payments are reviewed through secure gateway rules before an order is confirmed, reducing risk during checkout.
                        </p>
                    </div>
                    <div class="rounded-lg border border-sky-100 bg-sky-50 p-4">
                        <h3 class="font-semibold text-slate-900 mb-2">Responsible Monitoring</h3>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            Repeated failed logins or suspicious patterns lower trust and may require extra verification to protect the account.
                        </p>
                    </div>
                </div>

                <div class="rounded-lg bg-slate-900 p-6 text-white">
                    <h3 class="text-xl font-semibold mb-4">Trust Levels</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="font-semibold text-red-200">Low Trust: 0-30</p>
                            <p class="text-sm text-slate-200">Extra verification may be required before sensitive actions are allowed.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-amber-200">Medium Trust: 31-70</p>
                            <p class="text-sm text-slate-200">Normal access is allowed, with additional checks when activity looks unusual.</p>
                        </div>
                        <div>
                            <p class="font-semibold text-emerald-200">High Trust: 71-100</p>
                            <p class="text-sm text-slate-200">The account has a strong pattern of verified access and successful transactions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
