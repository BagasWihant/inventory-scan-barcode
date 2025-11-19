@section('title', 'Upload Search Assy')

<div class="max-w-7xl mx-auto" x-data="{
    isLoading: false,
    loadingText: 'Load Data . . .',
    doneOperation: null,
    mode: '',
    optionButton: true,
    bulan: '',
    thn: '',
    file: null,
    fileName: '',
    year: [],
    init() {
        let current = new Date().getFullYear();
        for (let i = current + 2; i >= current - 2; i--) {
            this.year.push(i);
        }
    },
    showAlert(message, timer = null, icon = 'warning', title = 'Perhatian') {
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            timer: timer,
            showConfirmButton: timer ? false : true,
            timerProgressBar: timer ? true : false,
        });
    },
    selectFile(e) {
        const allowed = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ];

        const file = e.target.files[0];

        if (!allowed.includes(file.type)) {
            this.showAlert('Hanya file Excel (.xls / .xlsx) yang diperbolehkan');
            e.target.value = '';
            return;
        }
        this.file = e.target.files[0];
        this.fileName = this.file ? this.file.name : '';
    },

    submitFile() {
        if (!this.file) return this.showAlert('Pilih file dulu!');
        if (!this.bulan) return this.showAlert('Pilih Bulan dulu!');
        if (!this.thn) return this.showAlert('Pilih Tahun dulu!');
        this.isLoading = true;
        this.loadingText = 'Uploading . . .';
        @this.call('uploadFile', [this.bulan, this.thn]).then((data) => {
            this.isLoading = false;
            this.loadingText = 'Load Data . . .';
            this.resetFile();
            this.doneOperation = 'File berhasil diupload dan disimpan di Database';
            console.log(data);
        });
    },
    resetFile() {
        this.file = null;
        this.fileName = '';
        document.querySelector('#fileInput').value = '';
    }
}" x-init="window.addEventListener('done-upload', e => {
    console.log('Event received:', e.detail);
    showAlert('Done saved to database', 3000, 'success', 'Success');
});"">
    <div class="text-2xl font-extrabold text-center">Upload Search Assy</div>
    <div class="flex justify-center gap-5 my-4" x-show="optionButton" x-transition>
        <button @click="mode = 'upload'; optionButton = false;"
            class="text-lg uppercase bg-gradient-to-br from-green-500 to-teal-400 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
            Upload Excel
        </button>
        <button @click="mode = 'search'; optionButton = false;"
            class="text-lg uppercase bg-gradient-to-br from-purple-500 to-blue-500 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
            Search
        </button>
    </div>
    <button @click="mode = ''; optionButton = true;" x-show="!optionButton" x-transition
        class="text-lg uppercase bg-gradient-to-br from-blue-500 to-teal-400 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
        Kembali
    </button>

    {{-- Upload  --}}
    <div x-show="mode == 'upload'" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95" class="border border-gray-300 p-6 my-4 rounded-xl shadow bg-white">
        <div class="flex gap-5">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Periode</label>
                <select
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
              focus:ring-blue-500 focus:border-blue-500"
                    x-model="bulan">
                    <option value="">Pilih Bulan</option>
                    <option value="1">Jan</option>
                    <option value="2">Feb</option>
                    <option value="3">Mar</option>
                    <option value="4">Apr</option>
                    <option value="5">Mei</option>
                    <option value="6">Jun</option>
                    <option value="7">Jul</option>
                    <option value="8">Ags</option>
                    <option value="9">Sep</option>
                    <option value="10">Okt</option>
                    <option value="11">Nov</option>
                    <option value="12">Des</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Tahun</label>
                <select x-model="thn"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
                focus:ring-blue-500 focus:border-blue-500 p-2">
                    <option value="">Pilih Tahun</option>
                    <template x-for="y in year" :key="y">
                        <option :value="y" x-text="y"></option>
                    </template>
                </select>
            </div>
        </div>

        <input id="fileInput" type="file" @change="selectFile"
            accept=".xlsx, .xls, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
            class="hidden" wire:model="file" />

        <label for="fileInput"
            class="flex items-center justify-between w-full my-4 cursor-pointer bg-gray-100 border border-gray-300 rounded-lg p-3 hover:bg-gray-200 transition relative">
            <span x-text="fileName || 'Pilih file Excel (.xls / .xlsx)'" class="text-gray-600 text-sm truncate"></span>
            <span x-show="!fileName"
                class="ml-4 px-4 py-2 text-sm font-semibold text-white 
                 bg-gradient-to-br from-green-500 to-cyan-500 
                 rounded-lg shadow hover:scale-105 transition">
                Browse
            </span>
            <button type="button" x-show="fileName" @click.stop="resetFile"
                class="ml-4 px-4 py-2 text-sm font-semibold text-white
                   bg-gradient-to-br from-red-500 to-pink-500
                   rounded-lg shadow hover:scale-105 transition">
                Hapus
            </button>
        </label>

        <div x-show="isLoading" class="flex flex-col items-center justify-center gap-2 mt-7">
            <svg class="animate-spin h-10 w-10 text-black" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                </path>
            </svg>
            <span x-text="loadingText" />
        </div>

        <template x-if="doneOperation">
            <p class="mt-3 text-sm mb-4 text-green-600 font-semibold">
                <span x-text="doneOperation"></span>
            </p>
        </template>

        <button @click="submitFile" x-show="file && !isLoading" x-transition
            class="w-full text-lg uppercase bg-gradient-to-br from-green-500 to-teal-400 text-white px-4 py-2 my-5 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
            Upload
        </button>
    </div>


    {{-- Search --}}
    <div class="flex my-4 gap-4" x-show="mode == 'search'" x-transition>
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor Assy</label>
            <input type="text" disabled
                class=" bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg ">
        </div>
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dari Tanggal</label>
            <input id="dtstart" type="date" onclick="this.showPicker()"
                class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Select date">
        </div>
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sampai Tanggal</label>
            <input id="dtend" type="date" onclick="this.showPicker()"
                class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Select date">
        </div>
        <div class="flex-col">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor Model</label>
            <input type="text"
                class=" bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="Nomor Model">
        </div>
    </div>

</div>
