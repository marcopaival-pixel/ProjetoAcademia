package br.com.nexshape.academia.data.local

import android.content.Context
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKey

enum class AppMode {
    STUDENT,
    PROFESSIONAL,
}

class SessionPreferences(context: Context) {
    private val prefs = EncryptedSharedPreferences.create(
        context,
        "nexshape_session_prefs",
        MasterKey.Builder(context).setKeyScheme(MasterKey.KeyScheme.AES256_GCM).build(),
        EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
        EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM,
    )

    fun getAppMode(): AppMode {
        val raw = prefs.getString(KEY_APP_MODE, null) ?: return AppMode.STUDENT
        return runCatching { AppMode.valueOf(raw) }.getOrDefault(AppMode.STUDENT)
    }

    fun setAppMode(mode: AppMode) {
        prefs.edit().putString(KEY_APP_MODE, mode.name).apply()
    }

    fun getActivePatientId(): Int? {
        val value = prefs.getInt(KEY_ACTIVE_PATIENT, -1)
        return if (value > 0) value else null
    }

    fun setActivePatientId(patientId: Int?) {
        if (patientId == null || patientId <= 0) {
            prefs.edit()
                .remove(KEY_ACTIVE_PATIENT)
                .remove(KEY_ACTIVE_PATIENT_NAME)
                .apply()
        } else {
            prefs.edit().putInt(KEY_ACTIVE_PATIENT, patientId).apply()
        }
    }

    fun getActivePatientName(): String? = prefs.getString(KEY_ACTIVE_PATIENT_NAME, null)

    fun setActivePatientName(name: String?) {
        if (name.isNullOrBlank()) {
            prefs.edit().remove(KEY_ACTIVE_PATIENT_NAME).apply()
        } else {
            prefs.edit().putString(KEY_ACTIVE_PATIENT_NAME, name).apply()
        }
    }

    fun setActivePatient(patientId: Int, name: String) {
        prefs.edit()
            .putInt(KEY_ACTIVE_PATIENT, patientId)
            .putString(KEY_ACTIVE_PATIENT_NAME, name)
            .apply()
    }

    fun clear() {
        prefs.edit().clear().apply()
    }

    companion object {
        private const val KEY_APP_MODE = "app_mode"
        private const val KEY_ACTIVE_PATIENT = "active_patient_id"
        private const val KEY_ACTIVE_PATIENT_NAME = "active_patient_name"
    }
}
