/**
 * Gerenciamento de Inatividade da Sessão
 * NexShape
 */

let inactivityTimeout = null;
let warningTimeout = null;
let hasRecentActivity = false;

// O tempo limite (em minutos) deve ser injetado na view Blade, ex: <meta name="session-timeout" content="30">
const getSessionTimeoutMinutes = () => {
    const meta = document.querySelector('meta[name="session-timeout"]');
    return meta ? parseInt(meta.getAttribute('content'), 10) : 30; // Default 30 min
};

const timeoutMinutes = getSessionTimeoutMinutes();
const timeoutMilliseconds = timeoutMinutes * 60 * 1000;
const warningMilliseconds = 2 * 60 * 1000; // Avisar 2 minutos antes de expirar

// Ping no backend a cada 5 minutos se houve atividade
const PING_INTERVAL = 5 * 60 * 1000;

const pingBackend = () => {
    if (hasRecentActivity) {
        axios.post('/api/session/ping', {}, {
            headers: { 'X-User-Activity': 'true' }
        }).catch(err => console.error('Ping falhou', err));
        hasRecentActivity = false;
    }
};

const showWarningModal = () => {
    const modal = document.getElementById('session-timeout-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
    
    // Tenta salvar rascunhos caso existam formulários "dirty"
    saveDraftsIfNeeded();
};

const logoutUser = () => {
    window.location.href = '/login?expired=1';
};

const resetTimers = () => {
    clearTimeout(warningTimeout);
    clearTimeout(inactivityTimeout);

    // Configura o aviso para (Tempo Total - 2 minutos)
    const timeUntilWarning = timeoutMilliseconds - warningMilliseconds;
    
    if (timeUntilWarning > 0) {
        warningTimeout = setTimeout(showWarningModal, timeUntilWarning);
        inactivityTimeout = setTimeout(logoutUser, timeoutMilliseconds);
    }
};

const registerActivity = () => {
    hasRecentActivity = true;
    resetTimers();
};

const saveDraftsIfNeeded = () => {
    // Busca formulários que tenham a classe 'auto-draft' ou atributo 'data-draft-id'
    const forms = document.querySelectorAll('form[data-draft-id]');
    
    forms.forEach(form => {
        // Coleta dados
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());
        const identifier = form.getAttribute('data-draft-id');
        
        axios.post('/api/drafts', {
            identifier: identifier,
            payload: payload
        }).catch(err => console.error('Falha ao salvar rascunho', err));
    });
};

// Listeners
document.addEventListener('DOMContentLoaded', () => {
    resetTimers();

    // Eventos que indicam atividade
    const events = ['click', 'keydown', 'mousemove', 'scroll'];
    
    // Throttle na captura de eventos (1x por segundo no máximo)
    let throttleTimer;
    const throttledRegisterActivity = () => {
        if (throttleTimer) return;
        throttleTimer = setTimeout(() => {
            registerActivity();
            throttleTimer = null;
        }, 1000);
    };

    events.forEach(event => {
        document.addEventListener(event, throttledRegisterActivity, { passive: true });
    });

    // Ping periódico
    setInterval(pingBackend, PING_INTERVAL);

    // Botão "Continuar conectado" no modal
    const renewBtn = document.getElementById('btn-session-renew');
    if (renewBtn) {
        renewBtn.addEventListener('click', () => {
            axios.post('/api/session/renew', {}, {
                headers: { 'X-User-Activity': 'true' }
            }).then(() => {
                const modal = document.getElementById('session-timeout-modal');
                if (modal) modal.classList.add('hidden');
                registerActivity();
            }).catch(() => {
                logoutUser();
            });
        });
    }
    
    const logoutBtn = document.getElementById('btn-session-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            axios.post('/logout').then(() => {
                window.location.href = '/login';
            });
        });
    }

    // Interceptor Axios para injetar X-User-Activity em ações reais
    if (window.axios) {
        window.axios.interceptors.request.use(config => {
            // Ignorar rotas conhecidas de background (polling)
            if (config.url && config.url.includes('/notifications/unread-counts')) {
                return config;
            }
            
            // Injetar header se for uma ação (POST/PUT/DELETE) ou se houver atividade recente
            if (config.method && config.method.toLowerCase() !== 'get' || hasRecentActivity) {
                if (!config.headers) config.headers = {};
                config.headers['X-User-Activity'] = 'true';
            }
            return config;
        });
    }
});
