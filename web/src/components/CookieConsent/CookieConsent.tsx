import React, { useState, useEffect } from 'react';
import { CookieConsentPreferences, CookieConsentProps } from './types';

const COOKIE_STORAGE_KEY = 'nexshape_cookie_consent';

const defaultPreferences: CookieConsentPreferences = {
  essential: true,
  analytics: false,
  marketing: false,
  preferences: false,
};

// Ícones SVG minimalistas
const IconCheck = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
    <polyline points="20 6 9 17 4 12"></polyline>
  </svg>
);

const IconSettings = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
    <circle cx="12" cy="12" r="3"></circle>
    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
  </svg>
);

const IconShield = () => (
  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="text-emerald-500">
    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
  </svg>
);

export const CookieConsent: React.FC<CookieConsentProps> = ({ onConsentChange }) => {
  const [isVisible, setIsVisible] = useState(false);
  const [showPreferences, setShowPreferences] = useState(false);
  const [preferences, setPreferences] = useState<CookieConsentPreferences>(defaultPreferences);
  const [hasLoaded, setHasLoaded] = useState(false);

  useEffect(() => {
    // Delay curto para animação de entrada mais fluida
    const timer = setTimeout(() => setHasLoaded(true), 500);
    
    try {
      const stored = localStorage.getItem(COOKIE_STORAGE_KEY);
      if (stored) {
        const parsed = JSON.parse(stored);
        setPreferences(parsed);
        if (onConsentChange) onConsentChange(parsed);
      } else {
        setIsVisible(true);
      }
    } catch (e) {
      console.error('Erro ao ler consentimento de cookies', e);
      setIsVisible(true);
    }

    return () => clearTimeout(timer);
  }, [onConsentChange]);

  const savePreferences = (newPrefs: CookieConsentPreferences) => {
    try {
      localStorage.setItem(COOKIE_STORAGE_KEY, JSON.stringify(newPrefs));
      setPreferences(newPrefs);
      setIsVisible(false);
      setShowPreferences(false);
      if (onConsentChange) onConsentChange(newPrefs);
    } catch (e) {
      console.error('Erro ao salvar consentimento de cookies', e);
    }
  };

  const handleAcceptAll = () => {
    savePreferences({
      essential: true,
      analytics: true,
      marketing: true,
      preferences: true,
    });
  };

  const handleRejectAll = () => {
    savePreferences({
      essential: true, // Sempre obrigatório
      analytics: false,
      marketing: false,
      preferences: false,
    });
  };

  const handleSavePreferences = () => {
    savePreferences(preferences);
  };

  const handleTogglePreference = (key: keyof CookieConsentPreferences) => {
    if (key === 'essential') return;
    setPreferences((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  if (!hasLoaded) return null;

  return (
    <>
      {/* Overlay Backdrop para quando as preferências estão abertas */}
      {showPreferences && isVisible && (
        <div className="fixed inset-0 bg-black/40 backdrop-blur-sm z-[9998] transition-opacity duration-300" />
      )}

      {/* Container Principal */}
      <div 
        className={`fixed bottom-0 left-0 right-0 z-[9999] p-4 sm:p-6 md:p-8 pointer-events-none transition-all duration-700 ease-out transform
          ${isVisible ? 'translate-y-0 opacity-100' : 'translate-y-full opacity-0'}
        `}
      >
        <div className="max-w-[1200px] mx-auto pointer-events-auto">
          <div 
            className={`
              bg-zinc-950/80 backdrop-blur-2xl border border-zinc-800/60 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.5)]
              rounded-2xl sm:rounded-3xl overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.23,1,0.32,1)]
              ${showPreferences ? 'max-w-3xl mx-auto' : 'max-w-4xl mx-auto md:flex md:items-center md:justify-between'}
            `}
          >
            
            {/* Visão Simplificada (Banner Inicial) */}
            {!showPreferences && (
              <div className="p-6 md:p-8 flex flex-col md:flex-row gap-6 md:gap-8 items-start md:items-center w-full">
                <div className="flex-1 flex gap-5 items-start">
                  <div className="w-12 h-12 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-center justify-center shrink-0 shadow-inner">
                    <IconShield />
                  </div>
                  <div>
                    <h3 className="text-lg font-bold text-white tracking-tight mb-2">Sua Privacidade</h3>
                    <p className="text-sm text-zinc-400 leading-relaxed font-medium">
                      Utilizamos cookies e tecnologias similares para otimizar sua experiência, analisar o tráfego e personalizar conteúdo. 
                      Ao continuar, você concorda com nossa{' '}
                      <a href="/legal/privacy" className="text-emerald-400 hover:text-emerald-300 underline underline-offset-2 transition-colors">Política de Privacidade</a>.
                    </p>
                  </div>
                </div>

                <div className="flex flex-col sm:flex-row gap-3 w-full md:w-auto shrink-0">
                  <button 
                    onClick={() => setShowPreferences(true)}
                    className="px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 text-sm font-semibold border border-zinc-800/50 hover:border-zinc-700 transition-all flex items-center justify-center gap-2 group"
                  >
                    <IconSettings />
                    <span>Preferências</span>
                  </button>
                  <button 
                    onClick={handleRejectAll}
                    className="px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 text-sm font-semibold border border-zinc-800/50 hover:border-zinc-700 transition-all"
                  >
                    Recusar
                  </button>
                  <button 
                    onClick={handleAcceptAll}
                    className="px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-sm font-bold shadow-[0_0_20px_-5px_rgba(16,185,129,0.4)] transition-all active:scale-[0.98]"
                  >
                    Aceitar Todos
                  </button>
                </div>
              </div>
            )}

            {/* Visão de Preferências Avançadas */}
            {showPreferences && (
              <div className="flex flex-col max-h-[80vh]">
                <div className="p-6 md:p-8 border-b border-zinc-800/60 flex items-start gap-5">
                  <div className="w-12 h-12 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-center justify-center shrink-0">
                    <IconSettings />
                  </div>
                  <div>
                    <h3 className="text-xl font-bold text-white tracking-tight mb-2">Preferências de Cookies</h3>
                    <p className="text-sm text-zinc-400 leading-relaxed font-medium">
                      Gerencie como utilizamos seus dados. Cookies essenciais são necessários para o funcionamento básico da plataforma.
                    </p>
                  </div>
                </div>

                <div className="p-6 md:p-8 space-y-6 overflow-y-auto custom-scrollbar">
                  <CookieToggleRow 
                    title="Essenciais (Estritamente Necessários)"
                    description="Necessários para o funcionamento da plataforma, segurança, login e persistência de sessão. Não podem ser desativados."
                    isActive={true}
                    isDisabled={true}
                    onToggle={() => {}}
                  />
                  <CookieToggleRow 
                    title="Analytics e Desempenho"
                    description="Ajudam-nos a entender como os visitantes interagem com o site, coletando e relatando informações anonimamente."
                    isActive={preferences.analytics}
                    isDisabled={false}
                    onToggle={() => handleTogglePreference('analytics')}
                  />
                  <CookieToggleRow 
                    title="Marketing e Publicidade"
                    description="Utilizados para rastrear visitantes em diferentes sites para exibir anúncios relevantes e engajadores."
                    isActive={preferences.marketing}
                    isDisabled={false}
                    onToggle={() => handleTogglePreference('marketing')}
                  />
                  <CookieToggleRow 
                    title="Preferências e Personalização"
                    description="Permitem que o site lembre de informações que mudam a forma como o site se comporta ou se parece."
                    isActive={preferences.preferences}
                    isDisabled={false}
                    onToggle={() => handleTogglePreference('preferences')}
                  />
                </div>

                <div className="p-6 md:p-8 border-t border-zinc-800/60 bg-zinc-900/20 flex flex-col sm:flex-row gap-3 justify-end">
                  <button 
                    onClick={handleRejectAll}
                    className="px-5 py-3 rounded-xl bg-transparent hover:bg-zinc-900 text-zinc-400 hover:text-zinc-300 text-sm font-semibold transition-all"
                  >
                    Recusar Todos
                  </button>
                  <button 
                    onClick={handleAcceptAll}
                    className="px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 text-sm font-semibold border border-zinc-800 hover:border-zinc-700 transition-all"
                  >
                    Aceitar Todos
                  </button>
                  <button 
                    onClick={handleSavePreferences}
                    className="px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-sm font-bold shadow-[0_0_20px_-5px_rgba(16,185,129,0.4)] transition-all active:scale-[0.98]"
                  >
                    Salvar Minhas Escolhas
                  </button>
                </div>
              </div>
            )}

          </div>
        </div>
      </div>
    </>
  );
};

// Componente Interno para as Linhas de Toggle
const CookieToggleRow = ({ 
  title, 
  description, 
  isActive, 
  isDisabled, 
  onToggle 
}: { 
  title: string, 
  description: string, 
  isActive: boolean, 
  isDisabled: boolean, 
  onToggle: () => void 
}) => {
  return (
    <div className="flex items-start gap-4 p-4 rounded-2xl border border-zinc-800/40 bg-zinc-900/30 hover:bg-zinc-900/50 transition-colors">
      <div className="flex-1">
        <div className="flex items-center gap-2 mb-1.5">
          <h4 className="text-base font-semibold text-zinc-200">{title}</h4>
          {isDisabled && <span className="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-400">Sempre Ativo</span>}
        </div>
        <p className="text-sm text-zinc-500 font-medium leading-relaxed">{description}</p>
      </div>
      <button 
        type="button"
        role="switch"
        aria-checked={isActive}
        disabled={isDisabled}
        onClick={onToggle}
        className={`
          relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent 
          transition-colors duration-300 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950
          ${isDisabled ? 'cursor-not-allowed opacity-60' : 'hover:scale-105 active:scale-95'}
          ${isActive ? 'bg-emerald-500' : 'bg-zinc-700'}
        `}
      >
        <span
          aria-hidden="true"
          className={`
            pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 
            transition duration-300 ease-in-out flex items-center justify-center
            ${isActive ? 'translate-x-5' : 'translate-x-0'}
          `}
        >
          {isActive && (
            <span className="text-emerald-500 flex transition-opacity duration-300">
              <IconCheck />
            </span>
          )}
        </span>
      </button>
    </div>
  );
};

export default CookieConsent;
