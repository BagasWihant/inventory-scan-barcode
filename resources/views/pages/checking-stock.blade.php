@if (isset($bypass))
<x-single-layout>
    @livewire('checking-stock')
</x-single-layout>
@else
<x-app-layout>
    @livewire('checking-stock')
</x-app-layout>
@endif