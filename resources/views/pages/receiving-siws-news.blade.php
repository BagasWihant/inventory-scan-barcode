@if (isset($bypass))
<x-single-layout>
    @livewire('receiving-s-i-w-s-news')
</x-single-layout>
@else
<x-app-layout>
    @livewire('receiving-s-i-w-s-news')
</x-app-layout>
@endif
