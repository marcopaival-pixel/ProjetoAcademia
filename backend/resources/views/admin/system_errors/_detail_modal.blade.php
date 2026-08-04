<div id="errorModal" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[100] hidden items-center justify-center p-6 sm:p-12 overflow-y-auto">
    <div class="bg-zinc-900/80 border border-white/10 rounded-[3rem] w-full max-w-6xl shadow-3xl flex flex-col max-h-full">
        <div class="p-10 border-b border-white/5 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-black text-red-500 uppercase tracking-[0.3em]">Log completo</span>
                <h2 class="text-lg font-bold text-white tracking-tight mt-1">Detalhes do erro</h2>
            </div>
            <button onclick="closeModal()" class="w-12 h-12 bg-zinc-950 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-white transition-all border border-white/5">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-10 overflow-y-auto space-y-8">
            <div id="modalMessage" class="p-6 bg-red-500/5 text-red-400 font-bold rounded-2xl border border-red-500/10 text-sm"></div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <pre id="modalPayload" class="bg-zinc-950 p-6 rounded-3xl border border-white/5 text-[10px] font-mono text-blue-400 overflow-auto min-h-[200px]"></pre>
                <pre id="modalStack" class="bg-zinc-950 p-6 rounded-3xl border border-white/5 text-[10px] font-mono text-zinc-500 overflow-auto min-h-[200px]"></pre>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-zinc-950/50 p-4 rounded-xl"><span class="text-zinc-600 text-[10px] font-black uppercase">IP</span><p id="modalIp" class="font-mono text-white"></p></div>
                <div class="bg-zinc-950/50 p-4 rounded-xl"><span class="text-zinc-600 text-[10px] font-black uppercase">User Agent</span><p id="modalUa" class="text-[10px] text-zinc-500"></p></div>
            </div>
        </div>
    </div>
</div>
<script>
function showErrorDetail(id) {
    const data = JSON.parse(document.getElementById('detail-' + id).innerText);
    document.getElementById('modalMessage').innerText = data.message;
    document.getElementById('modalPayload').innerText = JSON.stringify(data.payload, null, 2);
    document.getElementById('modalStack').innerText = data.stack || '';
    document.getElementById('modalIp').innerText = data.ip || '—';
    document.getElementById('modalUa').innerText = data.ua || '—';
    document.getElementById('errorModal').classList.replace('hidden', 'flex');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('errorModal').classList.replace('flex', 'hidden');
    document.body.style.overflow = 'auto';
}
</script>
