<div class="">

    <div class="flex min-h-screen">
        <ul
            class="flex-column space-y space-y-4 text-sm font-medium text-gray-500 dark:text-gray-400 md:me-4 mb-4 md:mb-0">
            <li>
                <button wire:click="showPdf(1)"
                    class="inline-flex items-center px-4 py-3 rounded-lg w-full @if ($active == 1) text-white bg-blue-700 @else text-gray-900 bg-gray-50 @endif  "
                    aria-current="page">
                    1
                </button>
            </li>
            <li>
                <button wire:click="showPdf(2)"
                    class="inline-flex items-center px-4 py-3 rounded-lg w-full @if ($active == 2) text-white bg-blue-700 @else text-gray-900 bg-gray-50 @endif ">
                    2
                </button>
            </li>
        </ul>
        <div class="p-6 bg-gray-50 text-medium text-gray-500 dark:text-gray-400 dark:bg-gray-800 rounded-lg w-full">

            @if ($urlPdf)
                <iframe src="{{ $urlPdf }}" frameborder="no" style="width:100%;min-height:90vh"></iframe>
            @endif
        </div>
    </div>


</div>
