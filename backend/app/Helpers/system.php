<?php

if (! function_exists('getSystemLoginUrl')) {
    /**
     * Get the official system login URL.
     *
     * @return string
     */
    function getSystemLoginUrl(): string
    {
        return config('system.login_url', url('/login'));
    }
}
