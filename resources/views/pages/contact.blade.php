<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Contact Us</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <h1 class="text-3xl font-bold mb-6">Contact Us</h1>

                <p class="text-gray-700 mb-4">
                    We are always here to support you. Reach out to SmartTrade Secure Platform for any inquiries, support, or assistance.
                </p>

                <h3 class="text-xl font-semibold mt-6 mb-2">Contact Information</h3>

                <ul class="text-gray-700 space-y-1 mb-4">
                    <li><strong>Name:</strong> Francis Bamugileki</li>
                    <li><strong>Email:</strong> francis@hesmb.com</li>
                    <li><strong>Phone:</strong> +255 689 045 666</li>
                </ul>

                <h3 class="text-xl font-semibold mt-6 mb-2">Location</h3>
                <p class="text-gray-700 mb-4">
                    Ngongona, Dodoma - Dodoma, Tanzania
                </p>

                <h3 class="text-xl font-semibold mt-6 mb-2">Support Message</h3>
                <p class="text-gray-700 mb-4">
                    For any technical issues, account problems, or order inquiries, our support team will respond within 24 hours.
                </p>

                <h3 class="text-xl font-semibold mt-6 mb-2">Send Message</h3>

                <form>
                    @csrf
                    <div class="mb-3">
                        <input type="text" placeholder="Your Name" class="form-control">
                    </div>
                    <div class="mb-3">
                        <input type="email" placeholder="Your Email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <textarea placeholder="Your Message" class="form-control" rows="4"></textarea>
                    </div>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
