package br.com.nexshape.academia.security

import java.security.MessageDigest
import java.security.SecureRandom
import java.util.Base64

object PinHasher {
    fun newSalt(): String {
        val bytes = ByteArray(16)
        SecureRandom().nextBytes(bytes)
        return Base64.getEncoder().encodeToString(bytes)
    }

    fun hash(pin: String, salt: String): String {
        val digest = MessageDigest.getInstance("SHA-256")
        val input = "$salt:$pin".toByteArray(Charsets.UTF_8)
        return Base64.getEncoder().encodeToString(digest.digest(input))
    }
}
