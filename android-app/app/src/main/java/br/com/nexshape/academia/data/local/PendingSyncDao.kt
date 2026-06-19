package br.com.nexshape.academia.data.local

import androidx.room.Dao
import androidx.room.Entity
import androidx.room.Insert
import androidx.room.PrimaryKey
import androidx.room.Query

@Entity(tableName = "pending_sync")
data class PendingSyncEntity(
    @PrimaryKey(autoGenerate = true) val id: Long = 0,
    val endpoint: String,
    val payloadJson: String,
    val createdAt: Long = System.currentTimeMillis(),
)

@Dao
interface PendingSyncDao {
    @Query("SELECT COUNT(*) FROM pending_sync")
    suspend fun pendingCount(): Int

    @Query("SELECT * FROM pending_sync ORDER BY createdAt ASC LIMIT :limit")
    suspend fun pending(limit: Int = 50): List<PendingSyncEntity>

    @Insert
    suspend fun insert(entity: PendingSyncEntity): Long

    @Query("DELETE FROM pending_sync WHERE id = :id")
    suspend fun delete(id: Long)
}
