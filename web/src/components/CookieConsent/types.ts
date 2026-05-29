export interface CookieConsentPreferences {
  essential: boolean; // Sempre true
  analytics: boolean;
  marketing: boolean;
  preferences: boolean;
}

export interface CookieConsentProps {
  onConsentChange?: (preferences: CookieConsentPreferences) => void;
  // Permite injetar lógica extra, como inicializar o Google Analytics se 'analytics' for true
}

export type CookieCategory = keyof CookieConsentPreferences;
