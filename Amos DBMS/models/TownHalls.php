<?php
class TownHalls {
    private $conn;
    private $table_name = "town_halls";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                (title, description, scheduled_date, duration_minutes, meeting_link, 
                created_by)
                VALUES
                (:title, :description, :scheduled_date, :duration_minutes, :meeting_link,
                :created_by)";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $stmt->bindParam(":title", htmlspecialchars(strip_tags($data['title'])));
        $stmt->bindParam(":description", htmlspecialchars(strip_tags($data['description'])));
        $stmt->bindParam(":scheduled_date", $data['scheduled_date']);
        $stmt->bindParam(":duration_minutes", $data['duration_minutes']);
        $stmt->bindParam(":meeting_link", htmlspecialchars(strip_tags($data['meeting_link'])));
        $stmt->bindParam(":created_by", $data['created_by']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getUpcoming() {
        $query = "SELECT t.*, 
                        u.username as creator_name,
                        COUNT(DISTINCT r.registration_id) as participant_count
                 FROM " . $this->table_name . " t
                 LEFT JOIN users u ON t.created_by = u.user_id
                 LEFT JOIN town_hall_registrations r ON t.town_hall_id = r.town_hall_id
                 WHERE t.scheduled_date > NOW() AND t.status = 'scheduled'
                 GROUP BY t.town_hall_id
                 ORDER BY t.scheduled_date ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getPast($limit = 5) {
        $query = "SELECT t.*, 
                        u.username as creator_name,
                        COUNT(DISTINCT r.registration_id) as participant_count
                 FROM " . $this->table_name . " t
                 LEFT JOIN users u ON t.created_by = u.user_id
                 LEFT JOIN town_hall_registrations r ON t.town_hall_id = r.town_hall_id
                 WHERE t.scheduled_date <= NOW() AND t.status IN ('completed', 'cancelled')
                 GROUP BY t.town_hall_id
                 ORDER BY t.scheduled_date DESC
                 LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function register($town_hall_id, $user_id) {
        // Check if already registered
        $check_query = "SELECT registration_id FROM town_hall_registrations 
                       WHERE town_hall_id = :town_hall_id AND user_id = :user_id";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bindParam(":town_hall_id", $town_hall_id);
        $check_stmt->bindParam(":user_id", $user_id);
        $check_stmt->execute();

        if ($check_stmt->fetch()) {
            return ['success' => false, 'message' => 'You are already registered for this town hall.'];
        }

        // Get current registration count
        $count_query = "SELECT COUNT(*) as count FROM town_hall_registrations WHERE town_hall_id = :town_hall_id";
        $count_stmt = $this->conn->prepare($count_query);
        $count_stmt->bindParam(":town_hall_id", $town_hall_id);
        $count_stmt->execute();
        $result = $count_stmt->fetch(PDO::FETCH_ASSOC);

        // For now, set a default max of 100 participants per town hall
        if ($result['count'] >= 100) {
            return ['success' => false, 'message' => 'This town hall is full.'];
        }

        $query = "INSERT INTO town_hall_registrations 
                (town_hall_id, user_id)
                VALUES (:town_hall_id, :user_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":town_hall_id", $town_hall_id);
        $stmt->bindParam(":user_id", $user_id);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Successfully registered for the town hall.'];
        }
        return ['success' => false, 'message' => 'Failed to register for the town hall.'];
    }

    public function unregister($town_hall_id, $user_id) {
        $query = "DELETE FROM town_hall_registrations 
                 WHERE town_hall_id = :town_hall_id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":town_hall_id", $town_hall_id);
        $stmt->bindParam(":user_id", $user_id);

        return $stmt->execute();
    }

    public function getStats() {
        $upcoming_count_query = "SELECT COUNT(*) as total_upcoming FROM " . $this->table_name . " WHERE scheduled_date > NOW() AND status = 'scheduled'";
        $past_count_query = "SELECT COUNT(*) as total_past FROM " . $this->table_name . " WHERE scheduled_date <= NOW() AND status = 'completed'";
        $registration_count_query = "SELECT COUNT(*) as total_registrations FROM town_hall_registrations";

        $upcoming_stmt = $this->conn->prepare($upcoming_count_query);
        $upcoming_stmt->execute();
        $upcoming_count = $upcoming_stmt->fetchColumn();

        $past_stmt = $this->conn->prepare($past_count_query);
        $past_stmt->execute();
        $past_count = $past_stmt->fetchColumn();

        $registration_stmt = $this->conn->prepare($registration_count_query);
        $registration_stmt->execute();
        $registration_count = $registration_stmt->fetchColumn();

        return [
            'total_upcoming' => $upcoming_count,
            'total_past' => $past_count,
            'total_registrations' => $registration_count
        ];
    }
}
?>
