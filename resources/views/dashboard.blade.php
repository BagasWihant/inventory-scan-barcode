<x-dashboard-layout>
    <div class="py-12">
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
    </div>
</x-dashboard-layout>
