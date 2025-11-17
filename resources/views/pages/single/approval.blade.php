<x-single-layout>
    <div>
        <div class="max-w-7xl mx-auto">
            <!-- top -->
            <div class="mb-14 flex justify-between ">
                <span class="text-xl font-bold">Doc Type : {{ $req->type }} </span>

                <span class="text-xl font-bold">Doc No : Kias/{{ $req->no_pr }} </span>
                <span class="text-xl font-bold">Doc Date :
                    {{ Carbon\Carbon::parse($req->tanggal_plan)->format('d-m-Y') }} </sp>
            </div>
            <!-- top -->
            @if ($req->status == 'O')
                <div class="text-center mb-14">
                    <span class="text-2xl font-bold text-green-600">Pengajuan ke Purchasing</span>
                </div>
            @elseif($req->status == 'PT')
                <div class="text-center mb-14">
                    <span class="text-2xl font-bold text-red-600">Ditolak Purchasing</span>
                </div>
            @elseif($req->status == 'AP')
                <div class="text-center mb-14">
                    <span class="text-2xl font-bold text-yellow-600">Pengajuan ke Supervisor</span>
                </div>
            @elseif($req->status == 'APT')
                <div class="text-center mb-14">
                    <span class="text-2xl font-bold text-red-600">Pengajuan ditolak Supervisor</span>
                </div>
            @elseif($req->status == 'AS')
                <div class="text-center mb-14">
                    <span class="text-2xl font-bold text-blue-600">Pengajuan ke Manajer</span>
                </div>
            @elseif($req->status == 'AMT')
                <div class="text-center mb-14">
                    <span class="text-2xl font-bold text-red-600">Pengajuan ditolek Manajer</span>
                </div>
            @elseif($req->status == 'AM')
                <div class="text-center mb-14">
                    <span class="text-2xl font-bold text-blue-600">Pengajuan disetujui Manajer</span>
                </div>
            @endif
            {{-- <div class="bg-slate-300 max-w-6xl p-4 rounded-xl shadow-md text-lg"> --}}
            {{-- <iframe src="{{ asset($req->pdf) }}" frameborder="no" class="iframe"
                    style="width:100%;min-height:150vh"></iframe> --}}
            {{-- </div> --}}
            <form action="{{ asset($req->pdf) }}" method="get" target="_blank" class="flex justify-center">
                <button
                    class="text-lg uppercase bg-gradient-to-br from-purple-500 to-blue-500 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
                    Buka Dokumen
                </button>
            </form>




            <div class="my-14 flex justify-between" x-data="{ modal: false, modalTitle: '', modalType: '', url: '' }">
                @if (in_array($req->status, ['O', 'AP', 'AS']))
                    @php
                        $reqSafe = clone $req;
                        unset($reqSafe->pdf_data);
                    @endphp
                    <div class="flex gap-8">
                        <form action="./2-approve" method="post">
                            @csrf
                            <input type="hidden" name="data" value="{{ json_encode($reqSafe) }}">
                            <button type="submit"
                                class="text-lg uppercase bg-gradient-to-br from-cyan-600 to-blue-700 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
                                Approve @if ($req->status == 'O')
                                    Purchasing
                                @elseif($req->status == 'AP')
                                    Supervisor
                                @else
                                    Manajer
                                @endif
                            </button>
                        </form>
                        @if ($req->status == 'AS')
                            <button
                                @click="modal = true;modalTitle = 'Reason to approve';modalType = 'approve';url = './2-approve';"
                                class="text-lg uppercase bg-gradient-to-br from-purple-500 to-blue-500 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
                                Reason Approve Manajer
                            </button>
                        @endif
                    </div>
                    <button
                        @click="modal = true;modalTitle = 'Reason to reject';modalType = 'reject';url = './2-reject';"
                        class="text-lg uppercase bg-gradient-to-br from-pink-600 to-red-700 text-white px-4 py-2 rounded-lg transition duration-500 ease-in-out hover:opacity-80 hover:backdrop-blur-md hover:scale-105">
                        reject
                    </button>
                    <!-- modal -->
                    <div x-show="modal" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 scale-90 bg-blur-sm"
                        x-transition:enter-end="opacity-100 scale-100 bg-blur-sm"
                        x-transition:leave="transition ease-in duration-200 transform"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
                        class="flex inset-0 backdrop-blur-md overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 max-h-full">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-2/4">

                            <h2 class="text-xl font-bold" x-text="modalTitle"></h2>

                            <form :action="url" method="POST" x-init="$refs.message.value = ''">
                                @csrf

                                <input type="hidden" name="data" value='@json($reqSafe)'>

                                <label class="block mb-2 text-sm font-medium">Reason</label>
                                <textarea x-ref="message" name="message" required rows="4"
                                    class="bg-gray-50 border border-gray-300 text-sm rounded-lg w-full p-2.5" placeholder="Write your message..."></textarea>

                                <div class="flex justify-end gap-4 mt-4">
                                    <button @click.prevent="modal = false" type="button"
                                        class="px-4 py-2 bg-gray-400 rounded-lg">
                                        Close
                                    </button>

                                    <button type="submit" class="px-4 py-2 text-white rounded-lg"
                                        :class="modalType == 'approve' ? 'bg-blue-500' : 'bg-red-500'"
                                        x-text="modalType.toUpperCase()"></button>
                                </div>
                            </form>

                        </div>
                    </div>

                @endif

            </div>
        </div>
    </div>

</x-single-layout>
