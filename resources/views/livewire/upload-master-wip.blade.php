<div class="max-w-7xl mx-auto" x-data="{
    isLoading: false,
    loadingText: '',
    file: null,
    fileName: null,
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
        this.isLoading = true;
        this.loadingText = 'Inserting to database...';
        @this.call('uploadFile').then((res) => {
            this.isLoading = false;
            this.loadingText = '';
            this.resetFile();
            this.showAlert('Done saved to database', 3000, 'success', 'Berhasil');
        });
    },
    resetFile() {
        this.file = null;
        this.fileName = '';
        document.querySelector('#fileInput').value = '';
    },
}">
    <input id="fileInput" type="file" @change="selectFile"
        accept=".xlsx, .xls, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
        class="hidden" wire:model="file" />

    <label for="fileInput" x-show="!isLoading"
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
        <span class="mt-3 mb-4 text-green-600 font-semibold" x-text="loadingText" />
    </div>
    {{-- <template x-if="doneOperation">
        <p class="mt-3 text-sm mb-4 text-green-600 font-semibold">
            <span x-text="doneOperation"></span>
        </p>
    </template> --}}

    <button @click="submitFile" x-show="file && !isLoading" x-transition
        class="w-full text-lg uppercase bg-gradient-to-br from-green-500 to-teal-400 text-white px-4 py-2 my-5 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
        Upload
    </button>
</div>
