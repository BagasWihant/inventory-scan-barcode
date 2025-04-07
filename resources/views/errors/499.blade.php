<x-single-layout>
    <div class="py-12 dark:text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" overflow-hidden shadow rounded-2xl">
                <div class="p-6 bg-red-500/30  text-xl text-center font-bold">
                    {{ $exception->getMessage() ?: 'Akses ditolak.' }}
                </div>
            </div>
        </div>
    </div>
</x-single-layout>
