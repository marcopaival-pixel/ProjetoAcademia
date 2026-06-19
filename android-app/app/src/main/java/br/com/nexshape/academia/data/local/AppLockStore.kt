package br.com.nexshape.academia.data.local

import android.content.Context
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey
import br.com.nexshape.academia.security.PinHasher

class AppLockStore(context: Context) {
    private val prefs = EncryptedSharedPreferences.create(
        context,
        "nexshape_app_lock",
        MasterKey.Builder(context).setKeyScheme(MasterKey.KeyScheme.AES256_GCM).build(),
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM,
    )

    fun isLockEnabled(): Boolean = prefs.getBoolean(KEY_LOCK_ENABLED, false)

    fun setLockEnabled(enabled: Boolean) {
        prefs.edit().putBoolean(KEY_LOCK_ENABLED, enabled).apply()
    }

    fun isBiometricEnabled(): Boolean = prefs.getBoolean(KEY_BIOMETRIC_ENABLED, true)

    fun setBiometricEnabled(enabled: Boolean) {
        prefs.edit().putBoolean(KEY_BIOMETRIC_ENABLED, enabled).apply()
    }

    fun hasPin(): Boolean = !prefs.getString(KEY_PIN_HASH, null).isNullOrBlank()

    fun setPin(pin: String) {
        val salt = PinHasher.newSalt()
        prefs.edit()
            .putString(KEY_PIN_SALT, salt)
            .putString(KEY_PIN_HASH, PinHasher.hash(pin, salt))
            .apply()
    }

    fun verifyPin(pin: String): Boolean {
        val salt = prefs.getString(KEY_PIN_SALT, null) ?: return false
        val hash = prefs.getString(KEY_PIN_HASH, null) ?: return false
        return PinHasher.hash(pin, salt) == hash
    }

    fun clearPin() {
        prefs.edit()
            .remove(KEY_PIN_SALT)
            .remove(KEY_PIN_HASH)
            .apply()
    }

    fun shouldRequireUnlock(biometricAvailable: Boolean): Boolean {
        if (!isLockEnabled()) return false
        if (hasPin()) return true
        return isBiometricEnabled() && biometricAvailable
    }

    companion object {
        private const val KEY_LOCK_ENABLED = "lock_enabled"
        private const val KEY_BIOMETRIC_ENABLED = "biometric_enabled"
        private const val KEY_PIN_SALT = "pin_salt"
        private const val KEY_PIN_HASH = "pin_hash"
    }
}
