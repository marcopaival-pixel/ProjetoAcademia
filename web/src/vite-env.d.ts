/// <reference types="vite/client" />

interface ImportMetaEnv {
  /** Base URL do Laravel (ex.: http://localhost:8000). Ver web/.env.example */
  readonly VITE_LARAVEL_URL?: string;
}

interface ImportMeta {
  readonly env: ImportMetaEnv;
}
