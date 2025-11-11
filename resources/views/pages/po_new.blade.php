@if (isset($bypass))
<x-single-layout>
    @livewire('purchase-order-in-new')
</x-single-layout>
@else
<x-app-layout>
    @livewire('purchase-order-in-new')
</x-app-layout>
@endif
