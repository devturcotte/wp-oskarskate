window.addEventListener('load', () => {
    const containerDialog = document.querySelectorAll('[data-dialog]');
    containerDialog.forEach((element) => {
        element.addEventListener('click', (e) => {
            const dialogId = e.target.getAttribute('data-dialog');
            const dialog = document.querySelector(dialogId);
            if (dialog.hasAttribute('open')) {
                dialog.setAttribute('closing', '');
                dialog.removeAttribute('open');
            } else {
                dialog.setAttribute('open', '');
                dialog.removeAttribute('closing');
            }
        });
    });

    const dialogs = document.querySelectorAll('.dialog');
    dialogs.forEach((dialog) => {

        dialog.addEventListener('click', (e) => {
            if (e.target === dialog) {
                dialog.setAttribute('closing', '');
                dialog.removeAttribute('open');
            }
        });
    });
    const btnClose = document.querySelectorAll('.btn-close')
    btnClose.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const dialog = e.target.closest('.dialog');
            dialog.setAttribute('closing', '');
            dialog.removeAttribute('open');
        });
    });
});