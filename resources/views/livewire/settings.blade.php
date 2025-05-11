<div x-init="init()" x-data="js()">
    <div class="max-w-7xl mx-auto mt-4">
        <div class="flex justify-between">
            <div class="">
                <p class="text-lg">Allow Add Material in Receiving</p>

                <p x-show="allowAddMaterialInReceiving == 1" class="text-green-500 font-bold">is Allowed</p>

                <p x-show="allowAddMaterialInReceiving == 0" class="text-red-600 font-bold">is Not Allowed</p>

            </div>
            <label class="switch">
                <input type="checkbox" :checked="allowAddMaterialInReceiving == 1"
                    @change="allowAddMaterialInReceivingChange($event.target.checked)">
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <script>
        function js() {
            return {
                allowAddMaterialInReceiving: false,
                init() {
                    this.allowAddMaterialInReceiving = {{ $allowAddMaterialInReceiving }}
                    console.log(this.allowAddMaterialInReceiving)
                },
                allowAddMaterialInReceivingChange(value) {
                    if (value) va = 1;
                    else va = 0

                    this.allowAddMaterialInReceiving = va
                    @this.call('allowAddMaterialInReceivingChange', va)
                },
            }
        }
    </script>
</div>
