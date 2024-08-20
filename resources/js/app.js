import './bootstrap';
import 'flowbite';
import { initFlowbite } from 'flowbite';
import Swal from 'sweetalert2';

document.addEventListener('livewire:navigated', () => {
    initFlowbite();
})
window.Swal = Swal;