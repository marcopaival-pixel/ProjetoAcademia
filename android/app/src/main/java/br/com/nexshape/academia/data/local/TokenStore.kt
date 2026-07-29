package br.com.nexshape.academia.data.local

import android.content.Context
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey

class TokenStore(context: Context) {
    private val prefs = EncryptedSharedPreferences.create(
        context,
        "nexshape_secure_prefs",
        MasterKey.Builder(context).setKeyScheme(MasterKey.KeyScheme.AES256_GCM).build(),
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM,
    )

    fun saveToken(token: String, email: String, name: String, expiresAt: String? = null) {
        prefs.edit()
            .putString(KEY_TOKEN, token)
            .putString(KEY_EMAIL, email)
            .putString(KEY_NAME, name)
            .apply {
                if (expiresAt.isNullOrBlank()) {
                    remove(KEY_EXPIRES_AT)
                } else {
                    putString(KEY_EXPIRES_AT, expiresAt)
                }
            }
            .apply()
    }

    fun getExpiresAt(): String? = prefs.getString(KEY_EXPIRES_AT, null)

    /**
     * Retorna true se o token expirou ou expira dentro de [withinMinutes].
     */
    fun isTokenExpiredOrExpiring(withinMinutes: Long = 60): Boolean {
        val raw = getExpiresAt() ?: return false
        return runCatching {
            val expires = java.time.Instant.parse(raw)
            val threshold = java.time.Instant.now().plusSeconds(withinMinutes * 60)
            !expires.isAfter(threshold)
        }.getOrDefault(false)
    }

    fun getToken(): String? = prefs.getString(KEY_TOKEN, null)

    fun getEmail(): String? = prefs.getString(KEY_EMAIL, null)

    fun getName(): String? = prefs.getString(KEY_NAME, null)

    fun isLoggedIn(): Boolean = !getToken().isNullOrBlank()

    fun clear() {
        prefs.edit().clear().apply()
    }

    fun saveActiveRole(role: String?) {
        prefs.edit().apply {
            if (role.isNullOrBlank()) {
                remove(KEY_ACTIVE_ROLE)
            } else {
                putString(KEY_ACTIVE_ROLE, role)
            }
        }.apply()
    }

    fun saveDefaultActiveRole(role: String?) {
        saveActiveRole(role)
        setActiveRoleConfirmed(false)
    }

    fun setActiveRoleConfirmed(confirmed: Boolean) {
        prefs.edit().putBoolean(KEY_ACTIVE_ROLE_CONFIRMED, confirmed).apply()
    }

    fun isActiveRoleConfirmed(): Boolean = prefs.getBoolean(KEY_ACTIVE_ROLE_CONFIRMED, false)

    fun getActiveRole(): String? = prefs.getString(KEY_ACTIVE_ROLE, null)

    fun saveAvailableRoles(roles: List<String>) {
        prefs.edit().putString(KEY_AVAILABLE_ROLES, roles.joinToString(",")).apply()
    }

    fun getAvailableRoles(): List<String> {
        val rolesStr = prefs.getString(KEY_AVAILABLE_ROLES, "") ?: ""
        if (rolesStr.isBlank()) return emptyList()
        return rolesStr.split(",")
    }

    fun saveActiveTenant(tenantId: String?) {
        prefs.edit().apply {
            if (tenantId.isNullOrBlank()) {
                remove(KEY_ACTIVE_TENANT)
            } else {
                putString(KEY_ACTIVE_TENANT, tenantId)
            }
        }.apply()
    }

    fun getActiveTenant(): String? = prefs.getString(KEY_ACTIVE_TENANT, null)

    fun saveActiveContextId(contextId: String?) {
        prefs.edit().apply {
            if (contextId.isNullOrBlank()) {
                remove(KEY_ACTIVE_CONTEXT_ID)
            } else {
                putString(KEY_ACTIVE_CONTEXT_ID, contextId)
            }
        }.apply()
    }

    fun getActiveContextId(): String? = prefs.getString(KEY_ACTIVE_CONTEXT_ID, null)

    companion object {
        private const val KEY_TOKEN = "access_token"
        private const val KEY_EMAIL = "user_email"
        private const val KEY_NAME = "user_name"
        private const val KEY_ACTIVE_ROLE = "active_role"
        private const val KEY_ACTIVE_ROLE_CONFIRMED = "active_role_confirmed"
        private const val KEY_AVAILABLE_ROLES = "available_roles"
        private const val KEY_ACTIVE_TENANT = "active_tenant"
        private const val KEY_ACTIVE_CONTEXT_ID = "active_context_id"
        private const val KEY_EXPIRES_AT = "token_expires_at"
    }
}
