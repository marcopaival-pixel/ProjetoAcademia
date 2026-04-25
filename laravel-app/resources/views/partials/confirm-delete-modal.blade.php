{{--
  Confirmação visual (substitui window.confirm).
  Formulários: data-confirm-delete data-confirm-title data-confirm-message [data-confirm-primary-label]
  JS (fetch, etc.): window.openNxConfirmDelete({ title, message, primaryLabel, onConfirm })
--}}
<div id="nx-confirm-delete-modal"
     class="fixed inset-0 z-[300] hidden flex items-center justify-center p-4 sm:p-6"
     role="dialog"
     aria-modal="true"
     aria-hidden="true"
     aria-labelledby="nx-confirm-delete-title-text">
    <div data-nx-confirm-backdrop class="absolute inset-0 bg-black/60 backdrop-blur-xl transition-opacity duration-300"></div>
    <div class="relative w-full max-w-md transform overflow-hidden rounded-3xl border border-white/10 bg-zinc-900/40 p-1 shadow-2xl backdrop-blur-2xl ring-1 ring-white/10 animate-[nxPopIn_0.3s_cubic-bezier(0.34,1.56,0.64,1)]">
        <div class="absolute inset-0 bg-gradient-to-br from-red-500/10 via-transparent to-transparent opacity-50"></div>
        
        <div class="relative bg-zinc-900/90 rounded-[1.4rem] p-8 sm:p-10">
            <div class="flex flex-col items-center text-center gap-6">
                <!-- Warning Icon with Pulse -->
                <div class="relative">
                    <div class="absolute inset-0 animate-ping rounded-full bg-red-500/20"></div>
                    <div class="relative flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500 to-rose-600 text-white shadow-xl shadow-red-500/20 ring-1 ring-white/20">
                        <i class="fas fa-trash-alt text-2xl" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="space-y-3">
                    <h2 id="nx-confirm-delete-title-text" class="text-2xl font-black tracking-tight text-white">
                        Confirmar Exclusão
                    </h2>
                    <p id="nx-confirm-delete-message" class="text-sm leading-relaxed text-zinc-400 font-medium">
                        Esta ação é irreversível e removerá permanentemente os dados selecionados.
                    </p>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <button type="button"
                        id="nx-confirm-delete-cancel"
                        class="order-2 sm:order-1 rounded-2xl border border-white/5 bg-white/5 px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 transition-all hover:bg-white/10 hover:text-white">
                    Cancelar
                </button>
                <button type="button"
                        id="nx-confirm-delete-confirm"
                        class="order-1 sm:order-2 rounded-2xl bg-gradient-to-r from-red-600 to-rose-600 px-6 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-white shadow-lg shadow-red-600/30 transition-all hover:brightness-110 hover:shadow-red-600/50 active:scale-95">
                    <span id="nx-confirm-delete-confirm-label">Confirmar</span>
                </button>
            </div>
        </div>
    </div>
</div>
<style>
    @keyframes nxPopIn {
        0% { opacity: 0; transform: scale(0.9) translateY(20px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>
<script>
(function () {
    var modal = document.getElementById('nx-confirm-delete-modal');
    if (!modal) return;

    var backdrop = modal.querySelector('[data-nx-confirm-backdrop]');
    var titleEl = document.getElementById('nx-confirm-delete-title-text');
    var msgEl = document.getElementById('nx-confirm-delete-message');
    var btnCancel = document.getElementById('nx-confirm-delete-cancel');
    var btnConfirm = document.getElementById('nx-confirm-delete-confirm');
    var btnConfirmLabel = document.getElementById('nx-confirm-delete-confirm-label');
    var pendingAction = null;

    function showModalUi(title, message, primaryLabel) {
        if (titleEl) titleEl.textContent = title;
        if (msgEl) msgEl.textContent = message;
        if (btnConfirmLabel) btnConfirmLabel.textContent = primaryLabel;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (btnConfirm) btnConfirm.focus();
    }

    function openModalFromForm(form) {
        var title = form.getAttribute('data-confirm-title') || 'Confirmar exclusão';
        var message = form.getAttribute('data-confirm-message') || 'Esta ação não pode ser desfeita. Deseja continuar?';
        var primaryLabel = form.getAttribute('data-confirm-primary-label') || 'Excluir';
        pendingAction = { type: 'form', el: form };
        showModalUi(title, message, primaryLabel);
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        pendingAction = null;
    }

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (form.dataset.confirmingDelete === '1') {
            delete form.dataset.confirmingDelete;
            return;
        }
        if (!form.hasAttribute('data-confirm-delete')) return;
        e.preventDefault();
        openModalFromForm(form);
    }, true);

    if (btnCancel) btnCancel.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (btnConfirm) {
        btnConfirm.addEventListener('click', function () {
            if (!pendingAction) return;
            if (pendingAction.type === 'callback') {
                var fn = pendingAction.fn;
                pendingAction = null;
                closeModal();
                fn();
                return;
            }
            var f = pendingAction.el;
            pendingAction = null;
            f.dataset.confirmingDelete = '1';
            closeModal();
            if (typeof f.requestSubmit === 'function') {
                f.requestSubmit();
            } else {
                f.submit();
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (modal.classList.contains('hidden')) return;
        closeModal();
    });

    // openNxConfirmDelete: opts = { title, message, primaryLabel, onConfirm } (para fetch DELETE, etc.)
    window.openNxConfirmDelete = function (opts) {
        if (!opts || typeof opts.onConfirm !== 'function') return;
        pendingAction = { type: 'callback', fn: opts.onConfirm };
        showModalUi(
            opts.title || 'Confirmar exclusão',
            opts.message || 'Esta ação não pode ser desfeita. Deseja continuar?',
            opts.primaryLabel || 'Excluir'
        );
    };
})();
</script>
