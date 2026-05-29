{{--
  Nexus Action Confirmation Modal (Premium Soft Aesthetic)
  Usage: window.openNxConfirmAction({ title, message, icon, type, confirmLabel, onConfirm })
  Types: 'success' (emerald), 'warning' (amber), 'danger' (red)
--}}
<div id="nx-confirm-action-modal"
     class="fixed inset-0 z-[10000] hidden items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-xl animate-fade-in"
     role="dialog"
     aria-modal="true">
    <div data-nx-confirm-backdrop class="absolute inset-0 transition-opacity duration-300"></div>
    
    <div class="relative w-full max-w-lg transform overflow-hidden rounded-[3.5rem] border border-white/10 bg-zinc-900/40 p-1 shadow-3xl ring-1 ring-white/10 animate-[nxPopIn_0.4s_cubic-bezier(0.34,1.56,0.64,1)]">
        <!-- Dynamic Gradient Glow -->
        <div id="nx-confirm-glow" class="absolute inset-0 opacity-20 blur-3xl transition-all duration-700"></div>
        
        <div class="relative bg-zinc-900/90 rounded-[3.3rem] p-10 sm:p-14 text-center space-y-8">
            <!-- Icon Container -->
            <div class="relative mx-auto">
                <div id="nx-confirm-pulse" class="absolute inset-0 animate-ping rounded-full opacity-20"></div>
                <div id="nx-confirm-icon-bg" class="relative w-24 h-24 rounded-[2rem] flex items-center justify-center mx-auto shadow-2xl transition-all duration-500 transform rotate-3 hover:rotate-0">
                    <div id="nx-confirm-icon-wrapper" class="text-white">
                        <!-- Lucide icon will be injected here -->
                    </div>
                </div>
            </div>

            <!-- Text Content -->
            <div class="space-y-4">
                <h2 id="nx-confirm-title" class="text-3xl font-black text-white tracking-tighter uppercase italic leading-none">
                    Confirmar Ação
                </h2>
                <p id="nx-confirm-message" class="text-zinc-500 font-medium leading-relaxed italic text-sm">
                    Deseja realmente prosseguir com esta operação crítica?
                </p>
            </div>

            <!-- Actions -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                <button type="button"
                        id="nx-confirm-cancel"
                        class="px-8 py-5 bg-zinc-950 border border-zinc-800 text-zinc-600 font-black rounded-3xl hover:text-white transition-all text-[10px] uppercase tracking-widest italic">
                    Cancelar
                </button>
                <button type="button"
                        id="nx-confirm-confirm"
                        class="px-8 py-5 font-black rounded-3xl transition-all shadow-xl text-[10px] uppercase tracking-widest italic active:scale-95 flex items-center justify-center gap-2">
                    <span id="nx-confirm-label">Confirmar</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes nxPopIn {
        0% { opacity: 0; transform: scale(0.9) translateY(40px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    
    .nx-confirm-success-glow { background: radial-gradient(circle at center, #10b981, transparent); }
    .nx-confirm-warning-glow { background: radial-gradient(circle at center, #f59e0b, transparent); }
    .nx-confirm-danger-glow { background: radial-gradient(circle at center, #ef4444, transparent); }
</style>

<script>
(function () {
    const modal = document.getElementById('nx-confirm-action-modal');
    if (!modal) return;

    const glow = document.getElementById('nx-confirm-glow');
    const pulse = document.getElementById('nx-confirm-pulse');
    const iconBg = document.getElementById('nx-confirm-icon-bg');
    const iconWrapper = document.getElementById('nx-confirm-icon-wrapper');
    const titleEl = document.getElementById('nx-confirm-title');
    const msgEl = document.getElementById('nx-confirm-message');
    const labelEl = document.getElementById('nx-confirm-label');
    const btnConfirm = document.getElementById('nx-confirm-confirm');
    const btnCancel = document.getElementById('nx-confirm-cancel');
    const backdrop = modal.querySelector('[data-nx-confirm-backdrop]');

    let pendingAction = null;

    const themes = {
        success: {
            glow: 'nx-confirm-success-glow',
            pulse: 'bg-emerald-500',
            iconBg: 'bg-emerald-500 shadow-emerald-500/20',
            btn: 'bg-emerald-500 text-zinc-950 hover:bg-emerald-400'
        },
        warning: {
            glow: 'nx-confirm-warning-glow',
            pulse: 'bg-amber-500',
            iconBg: 'bg-amber-500 shadow-amber-500/20',
            btn: 'bg-amber-500 text-zinc-950 hover:bg-amber-400'
        },
        danger: {
            glow: 'nx-confirm-danger-glow',
            pulse: 'bg-red-500',
            iconBg: 'bg-red-500 shadow-red-500/20',
            btn: 'bg-red-500 text-white hover:bg-red-400 shadow-red-500/20'
        }
    };

    window.openNxConfirmAction = function(opts) {
        const theme = themes[opts.type] || themes.warning;
        
        // Reset classes
        glow.className = 'absolute inset-0 opacity-20 blur-3xl transition-all duration-700 ' + theme.glow;
        pulse.className = 'absolute inset-0 animate-ping rounded-full opacity-20 ' + theme.pulse;
        iconBg.className = 'relative w-24 h-24 rounded-[2rem] flex items-center justify-center mx-auto shadow-2xl transition-all duration-500 transform rotate-3 hover:rotate-0 ' + theme.iconBg;
        btnConfirm.className = 'px-8 py-5 font-black rounded-3xl transition-all shadow-xl text-[10px] uppercase tracking-widest italic active:scale-95 flex items-center justify-center gap-2 ' + theme.btn;

        // Content
        titleEl.textContent = opts.title || 'Confirmar Ação';
        msgEl.innerHTML = opts.message || 'Deseja realmente prosseguir?';
        labelEl.textContent = opts.confirmLabel || 'Confirmar';
        
        // Icon
        iconWrapper.innerHTML = `<i data-lucide="${opts.icon || 'alert-triangle'}" class="w-12 h-12"></i>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        pendingAction = opts.onConfirm;

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        pendingAction = null;
    }

    btnConfirm.onclick = () => {
        const action = pendingAction;
        closeModal();
        if (typeof action === 'function') action();
    };

    btnCancel.onclick = closeModal;
    backdrop.onclick = closeModal;
})();
</script>
