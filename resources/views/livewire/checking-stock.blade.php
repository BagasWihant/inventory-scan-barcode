<div class="dark:text-white max-w-7xl mx-auto">

    <div class="fixed left-0 top-0  h-screen w-screen flex justify-center items-center bg-slate-300/70 z-50"
        wire:loading.flex wire:target="showData,changeReceivingMode">
        <div class="absolute animate-spin rounded-full h-32 w-32 border-t-4 border-b-4 border-purple-500"></div>
        <img src="https://www.svgrepo.com/show/509001/avatar-thinking-9.svg" class="rounded-full h-28 w-28">
    </div>

    <div class="text-2xl font-extrabold py-6 text-center">Receiving Report</div>
    <div class="flex justify-center gap-5">
        <button wire:click="changeReceivingMode('cnc')"
            class=" text-white font-bold p-3 text-xl rounded-xl @if ($mode == 'cnc') bg-blue-500 @else bg-gray-500 @endif"
            id="cncBtn"">Report
            CNC</button>
        <button wire:click="changeReceivingMode('sup')"
            class="text-white font-bold p-3 text-xl rounded-xl @if ($mode == 'sup') bg-blue-500 @else bg-gray-500 @endif"
            id="supplierBtn">Report
            Supplier</button>
    </div>

    @if ($mode == 'cnc')
        {{-- <x-receiving-report-cnc :listPalet="$listPalet" :listMaterial="$listMaterial" :receivingData="$receivingData" /> --}}
        @livewire('components.receiving-report-cnc')
        
    @elseif($mode == 'sup')
        <x-receiving-report-supplier :listPalet="$listPalet" :listMaterial="$listMaterial" :receivingData="$receivingData" :listPaletNoSup="$listPaletNoSup" />
    @endif
</div>

@script
    <script>
        $(document).ready(function() {

            $wire.on('popup', (event) => {
                Swal.fire({
                    timer: 1000,
                    title: event[0].title,
                    icon: "error",
                    showConfirmButton: false,
                    timerProgressBar: true,
                });


            })
        });
    </script>
@endscript
