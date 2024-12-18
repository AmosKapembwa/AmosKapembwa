<?php
class Initiative {
    private $conn;
    private $table_name = "initiatives";

    public $initiative_id;
    public $title;
    public $description;
    public $status;
    public $created_by;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new initiative
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (title, description, status, created_by)
                VALUES
                (:title, :description, :status, :created_by)";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Bind values
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":created_by", $this->created_by);

        try {
            if ($stmt->execute()) {
                $this->initiative_id = $this->conn->lastInsertId();
                return true;
            }
        } catch (PDOException $e) {
            error_log("Initiative creation error: " . $e->getMessage());
            return false;
        }
        return false;
    }

    // Get total count of initiatives with filters
    public function getCount($filters = array()) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $conditions = array();
        $params = array();

        if (!empty($filters['status'])) {
            $conditions[] = "status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $conditions[] = "category = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['location'])) {
            $conditions[] = "location LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Read all initiatives
    public function read($params = []) {
        $query = "SELECT i.*, 
                        u.username as creator_name,
                        COUNT(DISTINCT f.feedback_id) as feedback_count
                 FROM " . $this->table_name . " i
                 LEFT JOIN users u ON i.created_by = u.user_id
                 LEFT JOIN initiative_feedback f ON i.initiative_id = f.initiative_id";

        // Add WHERE clause if status filter is provided
        if (!empty($params['status'])) {
            $query .= " WHERE i.status = :status";
        }

        $query .= " GROUP BY i.initiative_id ORDER BY i.created_at DESC";

        try {
            $stmt = $this->conn->prepare($query);
            
            if (!empty($params['status'])) {
                $stmt->bindParam(":status", $params['status']);
            }

            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            error_log("Initiative read error: " . $e->getMessage());
            return false;
        }
    }

    // Update initiative
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET title = :title,
                    description = :description,
                    status = :status
                WHERE initiative_id = :initiative_id";

        try {
            $stmt = $this->conn->prepare($query);

            // Sanitize
            $this->title = htmlspecialchars(strip_tags($this->title));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->initiative_id = htmlspecialchars(strip_tags($this->initiative_id));

            // Bind values
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":initiative_id", $this->initiative_id);

            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            error_log("Initiative update error: " . $e->getMessage());
            return false;
        }
        return false;
    }

    // Delete initiative
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE initiative_id = :initiative_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":initiative_id", $this->initiative_id);

            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            error_log("Initiative deletion error: " . $e->getMessage());
            return false;
        }
        return false;
    }
}
?>
