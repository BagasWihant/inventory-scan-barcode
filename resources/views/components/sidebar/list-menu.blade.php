@props(['menuList'])

@php
    $menu = [
        [
            'url' => '#',
            'label' => 'Receiving',
            'child' => [
                ['url' => route('inventory.index'), 'label' => 'Receiving Material CNC'],
                ['url' => route('instock'), 'label' => 'Material Stock'],
                ['url' => route('checking'), 'label' => 'Check Stock'],
                ['url' => route('abnormal'), 'label' => 'Abnormal Material'],
            ],
        ],
        [
            'url' => '#',
            'label' => 'Stock Taking',
            'child' => [
                ['url' => route('prepare.stock.taking'), 'label' => 'Prepare Stock Taking', 'admin' => ''],
                ['url' => route('input.stock.taking'), 'label' => 'Input Stock Taking'],
                
            ],
        ],
    ];
@endphp

@if ($menuList)
    @foreach ($menu as $m)
        <li>
            @if (isset($m['child']))
                <button type="button"
                    class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-300 dark:text-white dark:hover:bg-gray-700"
                    aria-controls="dropdown-example{{ $loop->iteration }}"
                    data-collapse-toggle="dropdown-example{{ $loop->iteration }}">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 21">
                        <path
                            d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z" />
                    </svg>
                    <span class="flex-1 ms-3 pr-5 text-left rtl:text-right whitespace-nowrap">{{ $m['label'] }}</span>
                    <svg class="w-3 h-3 text-right" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-example{{ $loop->iteration }}" class="hidden py-2 space-y-2 pl-2 text-sm">
                    @foreach ($m['child'] as $ch)
                        @if (isset($ch['admin']))
                            @if (auth()->user()->Role_ID == '3' || auth()->user()->Admin == '1')
                                <li>
                                    <a href="{{ $ch['url'] }}"
                                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-300 dark:hover:bg-gray-700 group @if (url()->current() === $ch['url']) bg-gray-400 dark:bg-gray-500 @endif">
                                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                            viewBox="0 0 22 21">
                                            <path
                                                d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                            <path
                                                d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                        </svg>
                                        <span class="ms-3">{{ $ch['label'] }}</span>
                                    </a>
                                </li>
                            @endif
                        @else
                            <li>
                                <a href="{{ $ch['url'] }}"
                                    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-300 dark:hover:bg-gray-700 group @if (url()->current() === $ch['url']) bg-gray-400 dark:bg-gray-500 @endif">
                                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 22 21">
                                        <path
                                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                                        <path
                                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                                    </svg>
                                    <span class="ms-3">{{ $ch['label'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @else
                <a href="{{ $m['url'] }}" wire:navigate
                    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group @if (url()->current() === $m['url']) bg-gray-400 dark:bg-gray-500 @endif">
                    <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path
                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                        <path
                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                    </svg>
                    <span class="ms-3">{{ $m['label'] }}</span>
                </a>
            @endforelse
        </li>
    @endforeach
@else
    @foreach ($menu as $m)
        @if (isset($m['child']))
            @foreach ($m['child'] as $ch)
                @if (url()->current() === $ch['url'])
                    {{ $ch['label'] }}
                @endif
            @endforeach
        @else
            @if (url()->current() === $m['url'])
                {{ $m['label'] }}
            @endif
        @endif
    @endforeach
@endif
