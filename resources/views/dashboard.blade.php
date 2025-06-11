<x-dashboard-layout>
    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        
                        <a href="{{ route('instock') }}" class="block">
                            <div
                                class="bg-gradient-to-tr from-blue-500 to-blue-700 dark:from-blue-600 dark:to-blue-800
                                        rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition 
                                        duration-300 p-4 h-24 flex items-center justify-center text-center">
                                <span class="text-xl font-bold text-white">Warehouse</span>
                            </div>
                        </a>

                        <a href="{{ route('dashboard') }}" class="block">
                            <div
                                class="bg-gradient-to-tr from-green-500 to-green-700 dark:from-green-600 dark:to-green-800
                                        rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition 
                                        duration-300 p-4 h-24 flex items-center justify-center text-center">
                                <span class="text-xl font-bold text-white">System 1</span>
                            </div>
                        </a>

                        <a href="{{ route('dashboard') }}" class="block">
                            <div
                                class="bg-gradient-to-tr from-purple-500 to-purple-700 dark:from-purple-600 dark:to-purple-800
                                        rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition 
                                        duration-300 p-4 h-24 flex items-center justify-center text-center">
                                <span class="text-xl font-bold text-white">System 2</span>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div> --}}

    <div class="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700 mt-14">
        <div class="grid grid-cols-4 gap-4 mb-4">
            <a href="{{ route('instock') }}">
                <div class="flex flex-col items-center justify-center h-36 rounded-xl bg-gray-50 dark:bg-gray-800 p-3 
                            transition-all duration-300 ease-in-out transform hover:scale-105 
                            hover:shadow-lg hover:bg-gradient-to-br from-purple-300 to-blue-200 dark:hover:from-blue-900 dark:hover:to-gray-800">
                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 { fill: #04009a; }
                                    .cls-2 { fill: #77acf1; }
                                </style>
                            </defs>
                            <g data-name="23. Warehouse" id="_23._Warehouse">
                                <path class="cls-1" d="M31,30V15A15,15,0,0,0,1,15V30a1,1,0,0,0,0,2H31a1,1,0,0,0,0-2ZM18,24H14V20h4Zm-5,2h2v4H11V26Zm4,4V26h4v4Zm6,0V25a1,1,0,0,0-1-1H20V19a1,1,0,0,0-1-1H13a1,1,0,0,0-1,1v5H10a1,1,0,0,0-1,1v5H7V16H25V30Zm4,0V15a1,1,0,0,0-1-1H6a1,1,0,0,0-1,1V30H3V15a13,13,0,0,1,26,0V30Z" />
                                <path class="cls-2" d="M20,5a1,1,0,0,0-1-1H13a1,1,0,0,0-.38.08,1,1,0,0,0-.54.54A1,1,0,0,0,12,5v6a1,1,0,0,0,1,1h6a1,1,0,0,0,.38-.08,1,1,0,0,0,.54-.54A1,1,0,0,0,20,11ZM18,8.59,15.41,6H18ZM14,7.41,16.59,10H14Z" />
                            </g>
                        </g>
                    </svg>
                    <span class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Warehouse</span>
                </div>
            </a>
            <a href="{{ route('instock') }}">
                <div class="flex flex-col items-center justify-center h-36 rounded-xl bg-gray-50 dark:bg-gray-800 p-3 
                            transition-all duration-300 ease-in-out transform hover:scale-105 
                            hover:shadow-lg hover:bg-gradient-to-br from-purple-300 to-blue-200 dark:hover:from-blue-900 dark:hover:to-gray-800">
                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 { fill: #04009a; }
                                    .cls-2 { fill: #77acf1; }
                                </style>
                            </defs>
                            <g data-name="23. Warehouse" id="_23._Warehouse">
                                <path class="cls-1" d="M31,30V15A15,15,0,0,0,1,15V30a1,1,0,0,0,0,2H31a1,1,0,0,0,0-2ZM18,24H14V20h4Zm-5,2h2v4H11V26Zm4,4V26h4v4Zm6,0V25a1,1,0,0,0-1-1H20V19a1,1,0,0,0-1-1H13a1,1,0,0,0-1,1v5H10a1,1,0,0,0-1,1v5H7V16H25V30Zm4,0V15a1,1,0,0,0-1-1H6a1,1,0,0,0-1,1V30H3V15a13,13,0,0,1,26,0V30Z" />
                                <path class="cls-2" d="M20,5a1,1,0,0,0-1-1H13a1,1,0,0,0-.38.08,1,1,0,0,0-.54.54A1,1,0,0,0,12,5v6a1,1,0,0,0,1,1h6a1,1,0,0,0,.38-.08,1,1,0,0,0,.54-.54A1,1,0,0,0,20,11ZM18,8.59,15.41,6H18ZM14,7.41,16.59,10H14Z" />
                            </g>
                        </g>
                    </svg>
                    <span class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Approval</span>
                </div>
            </a>
            
            <a href="{{ route('instock') }}">
                <div class="flex flex-col items-center justify-center h-36 rounded-xl bg-gray-50 dark:bg-gray-800 p-3 
                            transition-all duration-300 ease-in-out transform hover:scale-105 
                            hover:shadow-lg hover:bg-gradient-to-br from-purple-300 to-blue-200 dark:hover:from-blue-900 dark:hover:to-gray-800">
                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 { fill: #04009a; }
                                    .cls-2 { fill: #77acf1; }
                                </style>
                            </defs>
                            <g data-name="23. Warehouse" id="_23._Warehouse">
                                <path class="cls-1" d="M31,30V15A15,15,0,0,0,1,15V30a1,1,0,0,0,0,2H31a1,1,0,0,0,0-2ZM18,24H14V20h4Zm-5,2h2v4H11V26Zm4,4V26h4v4Zm6,0V25a1,1,0,0,0-1-1H20V19a1,1,0,0,0-1-1H13a1,1,0,0,0-1,1v5H10a1,1,0,0,0-1,1v5H7V16H25V30Zm4,0V15a1,1,0,0,0-1-1H6a1,1,0,0,0-1,1V30H3V15a13,13,0,0,1,26,0V30Z" />
                                <path class="cls-2" d="M20,5a1,1,0,0,0-1-1H13a1,1,0,0,0-.38.08,1,1,0,0,0-.54.54A1,1,0,0,0,12,5v6a1,1,0,0,0,1,1h6a1,1,0,0,0,.38-.08,1,1,0,0,0,.54-.54A1,1,0,0,0,20,11ZM18,8.59,15.41,6H18ZM14,7.41,16.59,10H14Z" />
                            </g>
                        </g>
                    </svg>
                    <span class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Warehouse</span>
                </div>
            </a>
            
            <a href="{{ route('instock') }}">
                <div class="flex flex-col items-center justify-center h-36 rounded-xl bg-gray-50 dark:bg-gray-800 p-3 
                            transition-all duration-300 ease-in-out transform hover:scale-105 
                            hover:shadow-lg hover:bg-gradient-to-br from-purple-300 to-blue-200 dark:hover:from-blue-900 dark:hover:to-gray-800">
                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <defs>
                                <style>
                                    .cls-1 { fill: #04009a; }
                                    .cls-2 { fill: #77acf1; }
                                </style>
                            </defs>
                            <g data-name="23. Warehouse" id="_23._Warehouse">
                                <path class="cls-1" d="M31,30V15A15,15,0,0,0,1,15V30a1,1,0,0,0,0,2H31a1,1,0,0,0,0-2ZM18,24H14V20h4Zm-5,2h2v4H11V26Zm4,4V26h4v4Zm6,0V25a1,1,0,0,0-1-1H20V19a1,1,0,0,0-1-1H13a1,1,0,0,0-1,1v5H10a1,1,0,0,0-1,1v5H7V16H25V30Zm4,0V15a1,1,0,0,0-1-1H6a1,1,0,0,0-1,1V30H3V15a13,13,0,0,1,26,0V30Z" />
                                <path class="cls-2" d="M20,5a1,1,0,0,0-1-1H13a1,1,0,0,0-.38.08,1,1,0,0,0-.54.54A1,1,0,0,0,12,5v6a1,1,0,0,0,1,1h6a1,1,0,0,0,.38-.08,1,1,0,0,0,.54-.54A1,1,0,0,0,20,11ZM18,8.59,15.41,6H18ZM14,7.41,16.59,10H14Z" />
                            </g>
                        </g>
                    </svg>
                    <span class="mt-3 text-lg font-semibold text-gray-900 dark:text-white">Warehouse</span>
                </div>
            </a>
            
        </div>
    </div>
</x-dashboard-layout>
