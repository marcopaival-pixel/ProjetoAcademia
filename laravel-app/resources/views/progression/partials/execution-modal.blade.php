<!-- Execution Glass Modal (Vanilla JS) -->
<div id="executionModal" class="fixed inset-0 z-[110] hidden flex items-center justify-center p-4">
    <!-- Backdrop overlay -->
    <div onclick="closeExecutionModal()" class="absolute inset-0 bg-black/80 backdrop-blur-xl transition-opacity opacity-0" id="executionModalBackdrop"></div>
    
    <!-- Modal Dialog -->
    <div class="relative bg-[#0b0e14] border border-white/10 rounded-[2.5rem] shadow-[0_0_50px_rgba(0,0,0,0.8)] w-full max-w-5xl max-h-[95vh] flex flex-col transform scale-95 opacity-0 transition-all duration-300 pointer-events-auto overflow-hidden" id="executionModalDialog">
        
        <!-- Header -->
        <div class="absolute top-0 inset-x-0 p-6 flex justify-between items-start z-10 bg-gradient-to-b from-[#0b0e14]/90 to-transparent pointer-events-none">
            <div class="pointer-events-auto">
                <h3 id="execTitle" class="text-2xl font-black text-white drop-shadow-lg tracking-tight">Exercício</h3>
                <p id="execSubtitle" class="text-xs text-blue-400 font-black uppercase tracking-[0.2em] drop-shadow-md mt-1">Grupo Muscular</p>
            </div>
            <button type="button" onclick="closeExecutionModal()" class="pointer-events-auto w-10 h-10 flex items-center justify-center rounded-2xl bg-zinc-950/50 backdrop-blur-md border border-white/10 text-white hover:bg-white hover:text-black transition-all shadow-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="overflow-y-auto custom-scrollbar flex-1 relative flex flex-col lg:flex-row">
            
            <!-- Video Section (Left / Top) -->
            <div class="w-full lg:w-3/5 bg-black flex-shrink-0 relative min-h-[300px] lg:min-h-full flex items-center justify-center">
                <div id="execVideoContainer" class="w-full h-full aspect-video lg:aspect-auto">
                    <!-- Video content will be injected here -->
                    <div class="w-full h-full flex flex-col items-center justify-center text-zinc-600 bg-zinc-900">
                        <i class="fas fa-video-slash text-4xl mb-3"></i>
                        <span class="text-xs font-bold uppercase tracking-widest">Sem vídeo disponível</span>
                    </div>
                </div>
                
                <!-- Future AI Badge Placeholder -->
                <div class="absolute bottom-4 left-4 flex items-center gap-2 px-3 py-1.5 bg-zinc-950/60 backdrop-blur-md border border-white/10 rounded-xl pointer-events-none opacity-50" title="Narrador IA em breve">
                    <i class="fas fa-robot text-purple-400 text-xs"></i>
                    <span class="text-[9px] font-black text-purple-400 uppercase tracking-widest">AI Coach (Em breve)</span>
                </div>
            </div>

            <!-- Info Section (Right / Bottom) -->
            <div class="w-full lg:w-2/5 flex flex-col bg-zinc-900/20">
                <div class="p-6 lg:p-8 space-y-8 flex-1">
                    
                    <!-- Muscles Section -->
                    <div id="execMusclesSection" class="space-y-3">
                        <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em] flex items-center gap-2">
                            <i class="fas fa-child text-zinc-600"></i> Músculos Ativados
                        </h4>
                        <div id="execMusclesList" class="flex flex-wrap gap-2">
                            <!-- Injected -->
                        </div>
                    </div>

                    <!-- Tips Section -->
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black text-blue-500 uppercase tracking-[0.3em] flex items-center gap-2">
                            <i class="fas fa-lightbulb text-blue-500"></i> Dicas de Execução
                        </h4>
                        <ul id="execTipsList" class="space-y-3">
                            <!-- Injected -->
                        </ul>
                    </div>

                    <!-- Mistakes Section -->
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black text-red-500 uppercase tracking-[0.3em] flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-red-500"></i> Evite (Erros Comuns)
                        </h4>
                        <ul id="execMistakesList" class="space-y-3">
                            <!-- Injected -->
                        </ul>
                    </div>

                    <!-- Fallback Instructions -->
                    <div id="execInstructionsSection" class="space-y-3 hidden">
                        <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em]">Instruções Gerais</h4>
                        <div id="execInstructionsText" class="text-sm text-zinc-400 leading-relaxed font-medium">
                            <!-- Injected -->
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const execModal = document.getElementById('executionModal');
    const execBackdrop = document.getElementById('executionModalBackdrop');
    const execDialog = document.getElementById('executionModalDialog');

    function openExecutionModal(data) {
        // Populate Data
        document.getElementById('execTitle').innerText = data.name || 'Exercício';
        document.getElementById('execSubtitle').innerText = data.muscle_group || '';

        // Video Handling
        const videoContainer = document.getElementById('execVideoContainer');
        if (data.video_url) {
            if (data.video_type === 'youtube' || data.video_url.includes('youtube.com') || data.video_url.includes('youtu.be')) {
                // Extract Youtube ID - Manual String Manipulation (100% reliable)
                let videoId = '';
                try {
                    console.log('--- STARTING VIDEO PARSE ---');
                    console.log('Original URL:', data.video_url);
                    let url = data.video_url || '';
                    if (url.includes('v=')) {
                        videoId = url.split('v=')[1].split('&')[0].substring(0, 11);
                    } else if (url.includes('youtu.be/')) {
                        videoId = url.split('youtu.be/')[1].split('?')[0].substring(0, 11);
                    } else if (url.includes('shorts/')) {
                        videoId = url.split('shorts/')[1].split('?')[0].substring(0, 11);
                    } else if (url.includes('embed/')) {
                        videoId = url.split('embed/')[1].split('?')[0].substring(0, 11);
                    }
                    console.log('Extracted ID:', videoId);
                } catch (e) {
                    console.error('Error parsing YouTube URL:', e);
                }

                if (videoId) {
                    videoContainer.innerHTML = `<iframe class="w-full h-full object-cover" src="https://www.youtube.com/embed/${videoId}?autoplay=1&mute=0&loop=1&playlist=${videoId}&controls=1&modestbranding=1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>`;
                } else {
                    videoContainer.innerHTML = `<iframe class="w-full h-full" src="${data.video_url}" frameborder="0" allowfullscreen></iframe>`;
                }
            } else if (data.video_type === 'vimeo' || data.video_url.includes('vimeo.com')) {
                let videoId = '';
                try {
                    let url = data.video_url || '';
                    if (url.includes('vimeo.com/')) {
                         videoId = url.split('vimeo.com/')[1].split('?')[0].replace(/\//g, '');
                    }
                } catch(e) {
                    console.error('Error parsing Vimeo URL:', e);
                }
                
                if (videoId) {
                     videoContainer.innerHTML = `<iframe class="w-full h-full object-cover" src="https://player.vimeo.com/video/${videoId}?autoplay=1&loop=1&title=0&byline=0&portrait=0&muted=0" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
                } else {
                     videoContainer.innerHTML = `<iframe class="w-full h-full" src="${data.video_url}" frameborder="0" allowfullscreen></iframe>`;
                }
            } else if (data.video_type === 'gif' || data.video_url.endsWith('.gif') || data.video_url.includes('giphy.com')) {
                videoContainer.innerHTML = `<img src="${data.video_url}" class="w-full h-full object-cover" alt="GIF Execução">`;
            } else if (data.video_url.endsWith('.mp4') || data.video_url.endsWith('.webm')) {
                // HTML5 Video (Upload)
                videoContainer.innerHTML = `
                    <video class="w-full h-full object-cover" autoplay loop playsinline controls>
                        <source src="${data.video_url}" type="video/mp4">
                        Seu navegador não suporta vídeos.
                    </video>`;
            } else {
                // Fallback for other URLs, might be blocked by X-Frame-Options
                videoContainer.innerHTML = `<iframe class="w-full h-full" src="${data.video_url}" frameborder="0" allowfullscreen></iframe>`;
            }
        } else {
            videoContainer.innerHTML = `
                <div class="w-full h-full flex flex-col items-center justify-center text-zinc-600 bg-zinc-900 border-r border-white/5">
                    <i class="fas fa-video-slash text-4xl mb-3"></i>
                    <span class="text-xs font-bold uppercase tracking-widest">Sem vídeo</span>
                </div>`;
        }

        // Muscles
        const musclesList = document.getElementById('execMusclesList');
        musclesList.innerHTML = '';
        if (data.muscles && data.muscles.length > 0) {
            data.muscles.forEach(muscle => {
                musclesList.innerHTML += `<span class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-xs font-bold text-zinc-300 shadow-sm">${muscle}</span>`;
            });
            document.getElementById('execMusclesSection').classList.remove('hidden');
        } else {
            // Se não tiver músculos no array, usar o grupo muscular principal
            if(data.muscle_group) {
                musclesList.innerHTML = `<span class="px-3 py-1 bg-white/5 border border-white/10 rounded-lg text-xs font-bold text-zinc-300 shadow-sm">${data.muscle_group}</span>`;
                document.getElementById('execMusclesSection').classList.remove('hidden');
            } else {
                document.getElementById('execMusclesSection').classList.add('hidden');
            }
        }

        // Tips
        const tipsList = document.getElementById('execTipsList');
        tipsList.innerHTML = '';
        let hasTips = false;
        
        let parsedTips = [];
        if (typeof data.tips === 'string') {
            try { parsedTips = JSON.parse(data.tips); } catch(e) { parsedTips = [data.tips]; }
        } else if (Array.isArray(data.tips)) {
            parsedTips = data.tips;
        }

        if (parsedTips && parsedTips.length > 0 && parsedTips[0]) {
            parsedTips.forEach(tip => {
                if(!tip) return;
                hasTips = true;
                tipsList.innerHTML += `
                    <li class="flex items-start gap-3 bg-blue-500/5 p-3 rounded-xl border border-blue-500/10">
                        <i class="fas fa-check-circle text-blue-500 mt-0.5 text-sm"></i>
                        <span class="text-sm text-zinc-300 leading-tight">${tip}</span>
                    </li>`;
            });
        }
        
        if (!hasTips) {
            tipsList.innerHTML = `<li class="text-xs text-zinc-600 font-bold italic">Nenhuma dica cadastrada.</li>`;
        }

        // Mistakes
        const mistakesList = document.getElementById('execMistakesList');
        mistakesList.innerHTML = '';
        let hasMistakes = false;

        let parsedMistakes = [];
        if (typeof data.common_mistakes === 'string') {
            try { parsedMistakes = JSON.parse(data.common_mistakes); } catch(e) { parsedMistakes = [data.common_mistakes]; }
        } else if (Array.isArray(data.common_mistakes)) {
            parsedMistakes = data.common_mistakes;
        }

        if (parsedMistakes && parsedMistakes.length > 0 && parsedMistakes[0]) {
            parsedMistakes.forEach(mistake => {
                if(!mistake) return;
                hasMistakes = true;
                mistakesList.innerHTML += `
                    <li class="flex items-start gap-3 bg-red-500/5 p-3 rounded-xl border border-red-500/10">
                        <i class="fas fa-times-circle text-red-500 mt-0.5 text-sm"></i>
                        <span class="text-sm text-zinc-300 leading-tight">${mistake}</span>
                    </li>`;
            });
        }

        if (!hasMistakes) {
            mistakesList.innerHTML = `<li class="text-xs text-zinc-600 font-bold italic">Nenhum erro cadastrado.</li>`;
        }

        // Instructions Fallback
        const instSection = document.getElementById('execInstructionsSection');
        if (!hasTips && !hasMistakes && data.instructions) {
            instSection.classList.remove('hidden');
            document.getElementById('execInstructionsText').innerHTML = data.instructions.replace(/\n/g, '<br>');
        } else {
            instSection.classList.add('hidden');
        }

        // Open animation
        execModal.classList.remove('hidden');
        void execModal.offsetWidth; // Trigger reflow
        execBackdrop.classList.remove('opacity-0');
        execBackdrop.classList.add('opacity-100');
        execDialog.classList.remove('opacity-0', 'scale-95');
        execDialog.classList.add('opacity-100', 'scale-100');
    }

    function closeExecutionModal() {
        execBackdrop.classList.remove('opacity-100');
        execBackdrop.classList.add('opacity-0');
        execDialog.classList.remove('opacity-100', 'scale-100');
        execDialog.classList.add('opacity-0', 'scale-95');
        
        // Stop video/audio
        setTimeout(() => {
            const videoContainer = document.getElementById('execVideoContainer');
            videoContainer.innerHTML = ''; 
            execModal.classList.add('hidden');
        }, 300);
    }
</script>
