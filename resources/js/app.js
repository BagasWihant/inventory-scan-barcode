import './bootstrap';
import 'flowbite';
import { initFlowbite } from 'flowbite';
import Swal from 'sweetalert2';

document.addEventListener('livewire:navigated', () => {
    initFlowbite();
})

window.Toast = Swal.mixin({
    toast: true,
    position: 'bottom-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

window.showToast = function (message, icon = 'success') {
    window.Toast.fire({ icon, title: message });
};


window.showConfirm = function ({
    title       = 'Yakin?',
    text        = '',
    confirmText = 'Ya',
    cancelText  = 'Batal',
    icon        = 'warning',
} = {}) {
    return window.Swal.fire({
        title,
        text,
        icon,
        showCancelButton:   true,
        confirmButtonColor: '#6366f1',
        cancelButtonColor:  '#94a3b8',
        confirmButtonText:  confirmText,
        cancelButtonText:   cancelText,
        reverseButtons:     true,
    }).then(result => result.isConfirmed);
};

window.Swal = Swal;