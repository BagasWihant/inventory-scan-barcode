<x-single-layout>

    <div class="max-w-7xl mx-auto text-center rounded-md px-4 py-10 mt-6 bg-slate-300">

        <p class="text-2xl font-bold @if ($data['status'] == '0') text-red-700 @else text-green-500 @endif ">{{ $data['text'].' '.$data['posisi'] }}</p>
    </div>

</x-single-layout>