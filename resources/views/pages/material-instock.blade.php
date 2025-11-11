@if (isset($bypass))
<x-single-layout>
    @livewire('material-stock')
</x-single-layout>
@else
<x-app-layout>
    @livewire('material-stock')
</x-app-layout>
@endif
