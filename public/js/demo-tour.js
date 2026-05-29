const driver = window.driver.js.driver;

window.startNexShapeTour = function() {
    const tour = driver({
        showProgress: true,
        steps: [
            { 
                element: '.main-area', 
                popover: { 
                    title: 'Bem-vindo ao NexShape', 
                    description: 'Este é o seu centro de comando para performance e gestão.', 
                    side: "left", 
                    align: 'start' 
                } 
            },
            { 
                element: '[href*="dashboard"]', 
                popover: { 
                    title: 'Dashboard Inteligente', 
                    description: 'Aqui você visualiza métricas em tempo real, calorias, hidratação e o NexNeural (IA).', 
                    side: "right", 
                    align: 'start' 
                } 
            },
            { 
                element: '[href*="chat"]', 
                popover: { 
                    title: 'NexBot AI', 
                    description: 'Nossa inteligência artificial avançada que ajuda a prescrever treinos e tirar dúvidas técnicas.', 
                    side: "right", 
                    align: 'start' 
                } 
            },
            { 
                element: '[href*="training"]', 
                popover: { 
                    title: 'Academia NexShape', 
                    description: 'Módulo de treinamento para clientes e clínicas dominarem a plataforma.', 
                    side: "right", 
                    align: 'start' 
                } 
            },
            { 
                element: '.bg-gradient-to-r.from-indigo-600', 
                popover: { 
                    title: 'Controle de Demo', 
                    description: 'Use este painel flutuante para alternar perfis e resetar o ambiente.', 
                    side: "top", 
                    align: 'start' 
                } 
            },
        ],
        allowClose: true,
        overlayClickNext: false,
    });

    tour.drive();
};
