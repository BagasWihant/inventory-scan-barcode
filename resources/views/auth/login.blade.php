<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="flex h-screen w-full items-center justify-center bg-gradient-to-l from-sky-500 to-indigo-500">
        <div class="rounded-xl bg-gray-800 bg-opacity-50 px-16 py-10 shadow-lg backdrop-blur-md max-sm:px-8">
            <div class="text-white">
                <div class="mb-8 flex flex-col items-center">

                    <h1 class="mb-2 text-2xl">Inventory Scan Barcode</h1>
                    <span class="text-gray-300">Enter Login Details</span>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4 text-lg">
                        <input
                            class="rounded-3xl border-none bg-blue-400 bg-opacity-50 px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md"
                            type="text" name="username" placeholder="Username" />
                        <x-input-error :messages="$errors->get('user')" class="mt-2" />

                    </div>

                    <div class="mb-4 text-lg">
                        <input
                            class="rounded-3xl border-none bg-blue-400 bg-opacity-50 px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md"
                            type="Password" name="password" placeholder="Password" />
            <x-input-error :messages="$errors->get('Password')" class="mt-2" />

                    </div>
                    <div class="mt-8 flex justify-center text-lg text-black">
                        <button type="submit"
                            class="rounded-3xl bg-blue-400 bg-opacity-50 px-10 py-2 text-white shadow-xl backdrop-blur-md transition-colors duration-300 hover:bg-yellow-600">Login</button>
                    </div>
                    <a class="underline text-sm text-gray-100 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('register') }}">
                {{ __('Dont have an account?') }}
            </a>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
