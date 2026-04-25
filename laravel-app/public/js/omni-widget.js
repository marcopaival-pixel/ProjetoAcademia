(function() {
    // Configuration
    const CONFIG = {
        apiBase: window.location.origin + '/api/omnichannel',
        companySlug: 'academia-central', // Should be dynamic
        channelType: 'widget',
        primaryColor: '#6366f1',
    };

    // Create UI Elements
    const injectStyles = () => {
        const style = document.createElement('style');
        style.innerHTML = `
            #omni-widget-container {
                position: fixed;
                bottom: 30px;
                right: 30px;
                z-index: 9999;
                font-family: 'Inter', system-ui, -apple-system, sans-serif;
            }
            #omni-widget-btn {
                width: 60px;
                height: 60px;
                background: ${CONFIG.primaryColor};
                border-radius: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                cursor: pointer;
                box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
                transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
            #omni-widget-btn:hover { transform: scale(1.1); }
            
            #omni-window {
                position: absolute;
                bottom: 80px;
                right: 0;
                width: 380px;
                height: 550px;
                background: white;
                border-radius: 24px;
                box-shadow: 0 20px 50px rgba(0,0,0,0.15);
                display: none;
                flex-direction: column;
                overflow: hidden;
                border: 1px solid rgba(0,0,0,0.05);
                transition: all 0.3s ease;
            }
            #omni-window.active { display: flex; animation: slideUp 0.3s ease; }
            
            @keyframes slideUp { 
                from { opacity: 0; transform: translateY(20px); } 
                to { opacity: 1; transform: translateY(0); } 
            }

            .omni-header {
                background: ${CONFIG.primaryColor};
                padding: 25px;
                color: white;
            }
            .omni-messages {
                flex: 1;
                padding: 20px;
                overflow-y: auto;
                background: #f8fafc;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .omni-w-msg {
                padding: 10px 15px;
                border-radius: 18px;
                max-width: 80%;
                font-size: 14px;
            }
            .omni-w-msg-customer { align-self: flex-end; background: ${CONFIG.primaryColor}; color: white; border-bottom-right-radius: 4px; }
            .omni-w-msg-bot, .omni-w-msg-agent { align-self: flex-start; background: white; color: #1e293b; border-bottom-left-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

            .omni-input-area {
                padding: 15px;
                border-top: 1px solid #e2e8f0;
                display: flex;
                gap: 10px;
            }
            .omni-input {
                flex: 1;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 10px;
                outline: none;
            }
            .omni-send {
                background: ${CONFIG.primaryColor};
                color: white;
                border: none;
                padding: 0 15px;
                border-radius: 12px;
                cursor: pointer;
            }
        `;
        document.head.appendChild(style);
    };

    const createUI = () => {
        const container = document.createElement('div');
        container.id = 'omni-widget-container';
        container.innerHTML = `
            <div id="omni-window">
                <div class="omni-header">
                    <div style="font-weight: 700; font-size: 18px;">Atendimento On-line</div>
                    <div style="font-size: 12px; opacity: 0.8;">Olá! Como podemos ajudar hoje?</div>
                </div>
                <div class="omni-messages" id="omni-w-messages">
                    <div class="omni-w-msg omni-w-msg-bot">Olá! Seja bem-vindo à nossa central. No que posso ajudar?</div>
                </div>
                <div class="omni-input-area">
                    <input type="text" class="omni-input" id="omni-w-input" placeholder="Escreva sua mensagem...">
                    <button class="omni-send" id="omni-w-send">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
                    </button>
                </div>
            </div>
            <div id="omni-widget-btn">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path></svg>
            </div>
        `;
        document.body.appendChild(container);

        // Events
        const btn = document.getElementById('omni-widget-btn');
        const win = document.getElementById('omni-window');
        btn.onclick = () => win.classList.toggle('active');

        const input = document.getElementById('omni-w-input');
        const sendBtn = document.getElementById('omni-w-send');

        const sendMessage = async () => {
            const content = input.value.trim();
            if(!content) return;

            // Add to UI
            addMessageToUI('customer', content);
            input.value = '';

            // Send to API
            try {
                const res = await fetch(CONFIG.apiBase + '/receive', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        company_slug: CONFIG.companySlug,
                        channel_type: CONFIG.channelType,
                        customer_id: getCustomerId(),
                        customer_name: 'Visitante',
                        content: content
                    })
                });
                // Note: Em produção, o polling ou websocket leria a resposta.
            } catch(e) { console.error(e); }
        };

        sendBtn.onclick = sendMessage;
        input.onkeypress = (e) => { if(e.key === 'Enter') sendMessage(); };
    };

    const addMessageToUI = (sender, content) => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `omni-w-msg omni-w-msg-${sender}`;
        msgDiv.textContent = content;
        const container = document.getElementById('omni-w-messages');
        container.appendChild(msgDiv);
        container.scrollTop = container.scrollHeight;
    };

    const getCustomerId = () => {
        let id = localStorage.getItem('omni_customer_id');
        if(!id) {
            id = 'cust_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('omni_customer_id', id);
        }
        return id;
    };

    // Initialize
    injectStyles();
    createUI();
})();
