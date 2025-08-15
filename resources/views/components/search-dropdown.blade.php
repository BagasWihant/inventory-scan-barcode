<div x-data="searchDropdownComponent('{{ $method }}', '{{ $onSelect }}', '{{ $label }}', '{{ $resetEvent }}')" x-ref="dropdown" class="relative w-full">
    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <div class="gap-2 flex">
        <input type="text" x-model="search" @input.debounce.500ms="doSearch" placeholder="Search..." {{ $attributes }}
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring focus:border-blue-300 sm:text-sm">

        <div class="flex items-center" x-show="loading">
            <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                </path>
            </svg>
        </div>
    </div>
    <ul x-show="results.length > 0"
        class="absolute z-10 bg-white border mt-1 rounded-md w-full max-h-40 overflow-y-auto">
        <template x-for="item in results" :key="item.id">
            <li @click="selectItem(item)" class="cursor-pointer px-3 py-2 hover:bg-gray-100" x-text="item.product_no">
            </li>
        </template>
    </ul>
</div>

<script>
    function searchDropdownComponent(method, onSelect, label, resetEvent) {
        return {
            search: '',
            results: [],
            disabled: false,
            loading: false,

            init() {
                // Listen untuk custom reset event
                window.addEventListener(resetEvent, () => {
                    this.reset();
                });
            },

            async doSearch() {
                if (this.search.length < 3) {
                    this.results = [];
                    return;
                }
                this.loading = true;
                const res = await @this.call(method, this.search);
                this.loading = false;
                this.results = res;
            },

            selectItem(item) {
                this.search = item.product_no;
                this.results = [];
                @this.call(onSelect, item);
            },

            reset() {
                this.search = '';
                this.results = [];
            }
        }
    }
</script>
