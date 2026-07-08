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

    fun saveToken(token: String, email: String, name: String) {
        prefs.edit()
            .putString(KEY_TOKEN, token)
            .putString(KEY_EMAIL, email)
            .putString(KEY_NAME, name)
            .apply()
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

    companion object {
        private const val KEY_TOKEN = "access_token"
        private const val KEY_EMAIL = "user_email"
        private const val KEY_NAME = "user_name"
        private const val KEY_ACTIVE_ROLE = "active_role"
        private const val KEY_AVAILABLE_ROLES = "available_roles"
        private const val KEY_ACTIVE_TENANT = "active_tenant"
    }
}
