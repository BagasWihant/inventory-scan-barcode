<x-single-layout>
    <div class="py-12 dark:text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class=" overflow-hidden shadow rounded-2xl">
                <div class="p-6 bg-red-500/30  text-xl text-center font-bold">
                    {{ $data['msg'] ?: 'Akses ditolak.' }}
                </div>
            </div>
        </div>

        @if (isset($data['pdf']) && ($data['pdf'] != '' or !empty($data['pdf'])))
        <div class="max-w-7xl mx-auto text-center rounded-md px-4 py-10 mt-6 bg-slate-300">
            <iframe src="{{ asset($data['pdf']) }}" frameborder="no" class="iframe" style="width:100%;min-height:150vh"></iframe>
        </div>
        @endif
    </div>
</x-single-layout>
