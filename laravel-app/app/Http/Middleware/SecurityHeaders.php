<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Injeta headers de segurança HTTP em todas as respostas da aplicação.
 *
 * Headers aplicados:
 *  - X-Frame-Options: bloqueia clickjacking (iframes de terceiros)
 *  - X-Content-Type-Options: impede MIME sniffing
 *  - Referrer-Policy: limita dados de referência enviados a terceiros
 *  - X-XSS-Protection: proteção extra em browsers legados
 *  - Permissions-Policy: desativa features de hardware não usadas
 *  - Content-Security-Policy: base restritiva — ajustar conforme CDN/assets externos
 *  - Strict-Transport-Security: força HTTPS (ativado apenas fora de local/testing)
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Bloquear embedding em iframes de outras origens (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevenir MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Não enviar Referer para origens externas
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Proteção legada XSS (browsers mais antigos)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Desativar APIs de hardware não necessárias
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(self), usb=()'
        );

        // Content-Security-Policy: base segura
        // - 'unsafe-eval' adicionado para suporte completo ao Alpine.js e plugins
        // - Em local: permite servidor Vite (npm run dev, tipicamente :5173) para @vite carregar CSS/JS
        // - CDNs usados no layout (Font Awesome, etc.)
        $scriptSrc = "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.gstatic.com https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://unpkg.com";
        $styleSrc = "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net https://www.gstatic.com https://cdnjs.cloudflare.com https://cdn.tailwindcss.com https://cdn.jsdelivr.net";
        $connectSrc = "connect-src 'self' ws: wss: https://cdn.jsdelivr.net https://unpkg.com";

        if (app()->isLocal()) {
            // Em local, permite qualquer origem local/dev para facilitar Vite, Livewire, etc.
            $scriptSrc .= ' http: https: ws: wss:';
            $styleSrc .= ' http: https:';
            $connectSrc .= ' http: https: ws: wss:';
        }

        $csp = implode('; ', [
            "default-src 'self'",
            $scriptSrc,
            $styleSrc,
            "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net https://cdnjs.cloudflare.com https://cdn.jsdelivr.net",
            "img-src 'self' data: blob: https:",
            $connectSrc,
            "frame-src 'self' https://www.youtube.com https://player.vimeo.com https://vimeo.com https://youtu.be",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "object-src 'none'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS — apenas em produção (fora de local/testing para não bloquear dev HTTP)
        if (! app()->isLocal() && ! app()->runningUnitTests()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
