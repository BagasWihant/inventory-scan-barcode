<x-single-layout>
    <div>
        <div class="max-w-7xl mx-auto">
            <!-- top -->
            <div class="mb-14 flex justify-between ">
                <span class="text-xl font-bold">Doc Type : {{$data['docType']}} </span>

                <span class="text-xl font-bold">Doc No : Kias/{{$data['docNo']}} </span>
                <span class="text-xl font-bold">Doc Date : {{ $data['docDate'] }} </sp>
            </div>
            <!-- top -->
            @if($data['status'] == 'O')
            <div class="text-center mb-14">
                <span class="text-2xl font-bold text-green-600">Pengajuan ke Supervisor</span>
            </div>
            @elseif($data['status'] == 'OT')
            <div class="text-center mb-14">
                <span class="text-2xl font-bold text-yellow-600">Pengajuan ditolak Supervisor</span>
            </div>
            @elseif($data['status'] == 'AP')
            <div class="text-center mb-14">
                <span class="text-2xl font-bold text-yellow-600">Pengajuan ke Manajer</span>
            </div>
            @elseif($data['status'] == 'APT')
            <div class="text-center mb-14">
                <span class="text-2xl font-bold text-blue-600">Pengajuan ditolek Manajer</span>
            </div>
            @elseif($data['status'] == 'AM')
            <div class="text-center mb-14">
                <span class="text-2xl font-bold text-blue-600">Pengajuan disetujui Manajer</span>
            </div>
            @endif
            <div class="bg-slate-300 max-w-6xl p-4 rounded-xl shadow-md text-lg">
               <iframe src="{{ asset($data['pdf']) }}"
                {{-- width="100%" height="600px" --}}
               frameborder="no" class="iframe"
               style="width:100%;min-height:90vh"></iframe>
            </div>


            <div class="my-14 flex justify-between" x-data="{ modal: false }">
                @if (in_array($data['status'],['O','AP']))

                <form action="./approve" method="post">
                    @csrf
                    <input type="hidden" name="data" value="{{ json_encode($data) }}">
                    <button type="submit" class="text-lg uppercase bg-gradient-to-br from-cyan-600 to-blue-700 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
                        Approve @if ($data['status'] == 'O') Supervisor @else Manajer @endif
                    </button>
                </form>
                <button @click="modal = true" class="text-lg uppercase bg-gradient-to-br from-pink-600 to-red-700 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
                    reject
                </button>
                <!-- modal -->
                <div x-show="modal"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-90 bg-blur-sm"
                    x-transition:enter-end="opacity-100 scale-100 bg-blur-sm"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-90"
                    class="flex inset-0 backdrop-blur-md overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 max-h-full">
                    <div class="bg-white p-6 rounded-lg shadow-lg w-2/4">
                        <h2 class="text-xl font-bold">Reason to reject</h2>
                        <form action="./reject" method="post">
                            @csrf
                            <input type="hidden" name="data" value="{{ json_encode($data) }}">
                            <div>
                                <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reason</label>
                                <textarea id="reject_message" name="message" required rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your message here..."></textarea>

                                <!-- <textarea required></textarea> -->
                            </div>
                            <div class="flex justify-end gap-4">
                                <button @click="modal = false" class="uppercase mt-4 px-4 py-2 bg-gray-400 text-black rounded-lg">Close</button>
                                <button type="submit" @click="modal = false;" class="uppercase mt-4 px-4 py-2 bg-red-500 text-white rounded-lg">REJECT</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>

</x-single-layout>