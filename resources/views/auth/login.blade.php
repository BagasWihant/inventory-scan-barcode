<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    {{-- <div class="flex h-screen w-full items-center justify-center bg-gradient-to-l from-sky-500 to-indigo-500">
        <div class="rounded-xl bg-gray-800 bg-opacity-50 px-16 py-10 shadow-lg backdrop-blur-md max-sm:px-8">
            <div class="text-white">
                <div class="mb-8 flex flex-col items-center">

                    <h1 class="mb-2 text-2xl">Inventory Scan Barcode</h1>
                    <span class="text-gray-300">Enter Login Details</span>
                </div>
                
            </div>
        </div>
    </div> --}}
    <div class="flex h-screen">
        <!-- Left Pane -->
        <div class="w-full bg-gray-100 lg:w-1/2 flex items-center justify-center">
            <div class="max-w-md w-full p-6">
                <h1 class="text-3xl font-semibold mb-6 text-black text-center">Login</h1>
                <h1 class="text-sm font-semibold mb-6 text-gray-500 text-center">Please fill all fields</h1>
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4 text-lg">
                        <input
                            class="mt-1 p-2 w-full border rounded-md focus:border-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-300"
                            type="text" name="nik" placeholder="NIK" />
                        <x-input-error :messages="$errors->get('nik')" class="mt-2" />

                    </div>

                    <div class="mb-4 text-lg">
                        <input
                            class="mt-1 p-2 w-full border rounded-md focus:border-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-300"
                            type="Password" name="password" placeholder="Password" />
                        <x-input-error :messages="$errors->get('Password')" class="mt-2" />

                    </div>
                    <div class="mt-8 flex justify-center text-lg text-black">
                        <button type="submit"
                        class="w-full bg-black text-white p-2 rounded-md hover:bg-gray-800 focus:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors duration-300">Login</button>
                    </div>
                    <a class="underline text-sm text-gray-100 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        href="{{ route('register') }}">
                        {{ __('Dont have an account?') }}
                    </a>
                </form>

                <div class="mt-4 text-sm text-gray-600 text-center">
                    <p>Dont have an account? <a href="{{ route('register') }}" wire:navigate
                            class="text-black hover:underline">Register here</a>
                    </p>
                </div>
            </div>
        </div>
        <!-- Right Pane -->
        <div class="hidden lg:flex items-center justify-center flex-1 bg-white text-black">
            <div class="max-w-md text-center">
                <img src="{{ asset('assets/stock.svg') }}" alt="" class="w-[540px] h-[550px]">
                
            </div>
        </div>

    </div>
</x-guest-layout>
