<?php

/**
 * Todo Model
 *
 * This class handles all database interactions for todo items.
 * It provides methods for creating, reading, updating, and deleting todo items.
 *
 * @package Backend
 * @subpackage Models
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 */
class Todo
{
    /**
     * @var PDO The database connection object.
     */
    private PDO $conn;

    /**
     * @var string The name of the database table for todos.
     */
    private string $table_name = "todos";

    // Public properties to hold todo item data (optional, can be used for object representation)
    public int $id;
    public string $title;
    public ?string $completed_at; // Nullable datetime string
    public string $created_at;
    public string $updated_at;

    /**
     * Constructor to initialize the database connection.
     *
     * @param PDO $db The PDO database connection object.
     */
    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Retrieves all todo items from the database, ordered by creation date (descending).
     *
     * @return array An array of associative arrays, each representing a todo item.
     *               Returns an empty array if no todo items are found.
     */
    public function getAll(): array
    {
        $query = "SELECT id, title, completed_at, created_at, updated_at
                  FROM " . $this->table_name . "
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $todos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Convert 'completed_at' to boolean for frontend consistency if needed,
            // but for a model, returning raw DB value is often better.
            // Frontend can interpret 'null' as false and a date string as true.
            $todos[] = $row;
        }

        return $todos;
    }

    /**
     * Retrieves a single todo item by its ID.
     *
     * @param int $id The ID of the todo item to retrieve.
     * @return array|null An associative array representing the todo item, or null if not found.
     */
    public function getById(int $id): ?array
    {
        $query = "SELECT id, title, completed_at, created_at, updated_at
                  FROM " . $this->table_name . "
                  WHERE id = :id
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null; // Return the row or null if not found
    }

    /**
     * Creates a new todo item in the database.
     *
     * @param string $title The title of the new todo item.
     * @return int|false The ID of the newly created todo item on success, or false on failure.
     */
    public function create(string $title)
    {
        $query = "INSERT INTO " . $this->table_name . " (title) VALUES (:title)";

        $stmt = $this->conn->prepare($query);

        // Sanitize the title to prevent XSS attacks
        $sanitized_title = htmlspecialchars(strip_tags($title));

        // Bind parameters
        $stmt->bindParam(':title', $sanitized_title);

        if ($stmt->execute()) {
            return (int)$this->conn->lastInsertId();
        }

        return false;
    }

    /**
     * Updates an existing todo item's title and completion status.
     *
     * @param int $id The ID of the todo item to update.
     * @param string $title The new title for the todo item.
     * @param bool $completed The new completion status. True if completed, false otherwise.
     * @return bool True on success, false on failure.
     */
    public function update(int $id, string $title, bool $completed): bool
    {
        // Determine if completed_at should be set to current timestamp or null
        $completed_at = $completed ? date('Y-m-d H:i:s') : null;

        $query = "UPDATE " . $this->table_name . "
                  SET title = :title,
                      completed_at = :completed_at,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize the title
        $sanitized_title = htmlspecialchars(strip_tags($title));

        // Bind parameters
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $sanitized_title);
        // Use PDO::PARAM_NULL for null values, otherwise PDO::PARAM_STR
        $stmt->bindParam(':completed_at', $completed_at, $completed_at === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Toggles the completion status of a todo item.
     * This method is a specific case of update, focusing only on completion.
     *
     * @param int $id The ID of the todo item to update.
     * @param bool $completed The new completion status. True for complete, false for incomplete.
     * @return bool True on success, false on failure.
     */
    public function toggleComplete(int $id, bool $completed): bool
    {
        $completed_at = $completed ? date('Y-m-d H:i:s') : null;

        $query = "UPDATE " . $this->table_name . "
                  SET completed_at = :completed_at,
                      updated_at = CURRENT_TIMESTAMP
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':completed_at', $completed_at, $completed_at === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Deletes a todo item from the database.
     *
     * @param int $id The ID of the todo item to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}