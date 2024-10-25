@section('title', 'Portal Kerja')

<div class="">

    <div id="accordion-collapse" data-accordion="collapse">
        <h2 id="accordion-collapse-heading-1">
            <button type="button"
                class="flex items-center mb-1 justify-between w-full p-5 font-medium bg-slate-200 text-gray-800 border border-b-0 border-gray-200 rounded-xl "
                data-accordion-target="#accordion-collapse-body-1" aria-expanded="true"
                aria-controls="accordion-collapse-body-1">
                <span>menu 1</span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5 5 1 1 5" />
                </svg>
            </button>
        </h2>
        <div id="accordion-collapse-body-1" class="hidden" wire:ignore.self
            aria-labelledby="accordion-collapse-heading-1">
            <ul class="flex-col flex space-y gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">
                <li>
                    <button onclick="showPdf(1)" data-status="hide" id="btn1"
                        class="inline-flex items-center px-4 py-3 rounded-lg w-full  text-gray-900 bg-gray-50   "
                        aria-current="page">
                        1
                    </button>
                    <iframe src="{{ asset('RAB ME BENGKULU.pdf') }}" frameborder="no" class="iframe"
                        style="width:100%;min-height:90vh" id="framePdf1" hidden></iframe>
                </li>
                <li>
                    <button onclick="showPdf(2)" data-status="hide" id="btn2"
                        class="inline-flex items-center px-4 py-3 rounded-lg w-full  text-gray-900 bg-gray-50  ">
                        2
                    </button>
                    <iframe src="{{ asset('RAB ME BENGKULU.pdf') }}" frameborder="no" class="iframe"
                        style="width:100%;min-height:90vh" id="framePdf2" hidden></iframe>
                </li>
            </ul>
        </div>
        <h2 id="accordion-collapse-heading-2">
            <button type="button"
                class="flex items-center mb-1 justify-between w-full p-5 font-medium bg-slate-200 text-gray-800 border border-b-0 border-gray-200 rounded-xl "
                data-accordion-target="#accordion-collapse-body-2" aria-expanded="true"
                aria-controls="accordion-collapse-body-1">
                <span>menu 1</span>
                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5 5 1 1 5" />
                </svg>
            </button>
        </h2>
        <div id="accordion-collapse-body-2" class="hidden" wire:ignore.self
            aria-labelledby="accordion-collapse-heading-2">
            <ul class="flex-col flex space-y gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">
                <li>
                    <button onclick="showPdf(3)" data-status="hide" id="btn3"
                        class="inline-flex items-center px-4 py-3 rounded-lg w-full text-gray-900 bg-gray-50  "
                        aria-current="page">
                        3
                    </button>
                    <iframe src="{{ asset('RAB ME BENGKULU.pdf') }}" frameborder="no" class="iframe"
                        style="width:100%;min-height:90vh" id="framePdf3" hidden></iframe>
                </li>
                <li>
                    <button onclick="showPdf(4)" data-status="hide" id="btn4"
                        class="inline-flex items-center px-4 py-3 rounded-lg w-full text-gray-900 bg-gray-50 ">
                        4
                    </button>
                    <iframe src="{{ asset('RAB ME BENGKULU.pdf') }}" frameborder="no" class="iframe"
                        style="width:100%;min-height:90vh" id="framePdf4" hidden></iframe>
                </li>
            </ul>
        </div>
    </div>


    <script>
        function showPdf(id) {
            status = $('#btn' + id).attr('data-status');
            $('iframe').hide();
            if (status == 'hide') {
                $('#btn' + id).attr('data-status', 'show');
                $('#framePdf' + id).show();
            } else {
                $('#btn' + id).attr('data-status', 'hide');
                $('#framePdf' + id).hide();
            }
        }
    </script>
</div>
