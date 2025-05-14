<div x-init="init()" x-data="js()">
    <div class="max-w-7xl mx-auto mt-4">
        <div class="flex justify-between">
            <div class="">
                <p class="text-lg">Allow Add Material in Receiving</p>


            </div>
            <span class="cursor-pointer text-blue-600 font-bold" @click="openModal('allow-add-material')">Change
                Status</span>
            {{-- <label class="switch">
                <input type="checkbox" :checked="allowAddMaterialInReceiving == 1"
                    @change="allowAddMaterialInReceivingChange($event.target.checked)">
                <span class="slider round"></span>
            </label> --}}
        </div>
    </div>
    <div id="static-modal" data-modal-backdrop="static" tabindex="-1" x-show="showModal" x-cloak
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-90 backdrop-blur-sm"
        x-transition:enter-end=" scale-100 backdrop-blur-md" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start=" scale-100" x-transition:leave-end="scale-90"
        class="flex inset-0 sc backdrop-blur-md overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 max-h-full">
        <div class="relative p-4 w-full max-w-7xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="sticky top-0 z-40 bg-white">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 ">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white"
                            x-text="replace(nameModal, '-', ' ')"></h3>
                        <button type="button" @click="closeModal"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                </div>
                <form x-ref="form">
                    <div class="p-4" x-html="modalContent" ></div>
                </form>

                <!-- Modal footer -->
                <div
                    class="flex items-center justify-end p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 sticky bottom-0 bg-white">

                    <button @click="closeModal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Close</button>
                    <button @click="saveModal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-blue-500 rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Save</button>

                </div>
            </div>
        </div>

    </div>
    <script>
        function js() {
            return {
                allowAddMaterialInReceiving: false,
                showModal: false,
                modalContent: '',
                nameModal: '',
                form: {},
                replace(str, find, replaceWith) {
                    return str.replaceAll(find, replaceWith).toUpperCase();
                },
                init() {},
                allowAddMaterialInReceivingChange(value) {
                    if (value) va = 1;
                    else va = 0

                    this.allowAddMaterialInReceiving = va
                    @this.call('allowAddMaterialInReceivingChange', va)
                },
                openModal: async function(name) {
                    this.nameModal = name;
                    console.log(this.nameModal);
                    try {
                        const data = await @this.call('getData', name);
                        this.showModal = true;
                        console.log(data);
                        this.modalContent = `
                        <div class="p-4 grid grid-cols-2">
                            ${data.map(item => `
                            <div class="flex gap-4 my-4">
                                <label class="switch">
                                    <input type="checkbox" name="${item.i}" ${item.s == 1 ? 'checked' : ''}>
                                    <span class="slider round"></span>
                                    </label>
                                <span>${item.u}</span>
                            </div>
                            `).join('')}
                        </div>`
                        ;

                    } catch (error) {
                        console.error('Gagal ambil data modal:', error);
                    }
                },
                closeModal() {
                    this.showModal = false
                    this.modalContent = ''
                    this.nameModal = ''
                },
                saveModal() {
                    const form = this.$refs.form;
                    const inputs = form.querySelectorAll('input[type="checkbox"]');
                    const result = {};

                    inputs.forEach(input => {
                        result[input.name] = input.checked ? 1 : 0;

                    });
                    @this.call('saveData', result, this.nameModal).then((res) => {
                        this.closeModal()
                        if (res == 'success') {
                            Swal.fire({
                                timer: 1500,
                                title: 'Data Terupdate',
                                icon: 'success',
                                showConfirmButton: false,
                                timerProgressBar: true,
                            });
                            return;
                        } else {
                            Swal.fire({
                                timer: 1500,
                                title: 'Data Gagal Terupdate',
                                icon: 'error',
                                showConfirmButton: false,
                                timerProgressBar: true,
                            });
                        }
                    });
                }
            }
        }
    </script>
</div>
