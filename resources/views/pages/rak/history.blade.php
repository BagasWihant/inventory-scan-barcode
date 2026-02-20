<x-single-livewire-layout>
    @if (isset($rak_id))
        @livewire('rak.history', ['rak_id' => $rak_id])
    @else
        @livewire('rak.history')
    @endif
</x-single-livewire-layout>
