<x-guest-layout>

    <div class="flex h-screen w-full items-center justify-center  bg-gradient-to-r from-sky-500 to-indigo-500">
        <div class="rounded-xl bg-gray-800 bg-opacity-50 px-16 py-10 shadow-lg backdrop-blur-md max-sm:px-8">
            <div class="text-white">
                <div class="mb-8 flex flex-col items-center">
                    <h1 class="mb-2 text-2xl">Inventory Scan Barcode</h1>
                    <span class="text-gray-300">Enter Login Details</span>
                </div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4 text-lg">
                        <input
                            class="rounded-3xl border-none bg-blue-400 bg-opacity-50 px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md"
                            type="text" name="nik" value="{{ old('nik') }}" placeholder="NIK" />
                        <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                    </div>
                    
                    <div class="mb-4 text-lg">
                        <input
                            class="rounded-3xl border-none bg-blue-400 bg-opacity-50 px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md"
                            type="text" name="name" value="{{ old('name') }}" placeholder="Name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4 text-lg">
                        <input
                            class="rounded-3xl border-none bg-blue-400 bg-opacity-50 px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md"
                            type="email" name="email" value="{{ old('email') }}" placeholder="Email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />

                    </div>

                    <div class="mb-4 text-lg text-center">
                        <select id="section" name="section" value="{{ old('section') }}" class="rounded-3xl border-none bg-blue-400 bg-opacity-50 px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md">
                            <option>Section</option>
                          @php
                            DB::table('section')->select('id', 'section')->where('active', 1)->get()->map(function ($item) {
                              echo '<option value="' . $item->id . '">' . $item->section . '</option>';
                            })->implode('')
                          @endphp
                        </select>
                      </div>
                      
                    <div class="mb-4 text-lg">
                        <input
                            class="rounded-3xl border-none bg-blue-400 bg-opacity-50 px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md"
                            type="Password" name="password" placeholder="Password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="mb-4 text-lg">
                        <input
                            class="rounded-3xl border-none bg-blue-400 bg-opacity-50 px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md"
                            type="Password" name="password_confirmation" placeholder="Password Confirmation" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                    <div class="mt-8 flex flex-col gap-7 justify-center text-lg text-black">
                        <button type="submit" class="rounded-3xl bg-blue-400 bg-opacity-50 px-10 py-2 text-white shadow-xl backdrop-blur-md transition-colors duration-300 hover:bg-yellow-600">Register</button>
                        <a class="underline text-sm text-gray-100 dark:text-gray-100 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
