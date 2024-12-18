<?php
class Suggestion {
    private $conn;
    private $table_name = "suggestions";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . "
                (title, description, category, location, created_by)
                VALUES (:title, :description, :category, :location, :created_by)";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $stmt->bindParam(":title", htmlspecialchars(strip_tags($data['title'])));
        $stmt->bindParam(":description", htmlspecialchars(strip_tags($data['description'])));
        $stmt->bindParam(":category", $data['category']);
        $stmt->bindParam(":location", htmlspecialchars(strip_tags($data['location'])));
        $stmt->bindParam(":created_by", $data['created_by']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function read($filters = array(), $sort = array()) {
        $query = "SELECT s.*, 
                        u.name as creator_name,
                        COUNT(DISTINCT c.comment_id) as comment_count
                 FROM " . $this->table_name . " s
                 LEFT JOIN users u ON s.created_by = u.username
                 LEFT JOIN suggestion_comments c ON s.suggestion_id = c.suggestion_id";

        $conditions = array();
        $params = array();

        if (!empty($filters)) {
            if (isset($filters['category'])) {
                $conditions[] = "s.category = :category";
                $params[':category'] = $filters['category'];
            }
            if (isset($filters['status'])) {
                $conditions[] = "s.status = :status";
                $params[':status'] = $filters['status'];
            }
            if (isset($filters['location'])) {
                $conditions[] = "s.location LIKE :location";
                $params[':location'] = '%' . $filters['location'] . '%';
            }
            if (isset($filters['search'])) {
                $conditions[] = "(s.title LIKE :search OR s.description LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
        }

        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $query .= " GROUP BY s.suggestion_id";

        if (!empty($sort)) {
            $valid_columns = ['votes', 'created_at', 'comment_count'];
            $valid_directions = ['ASC', 'DESC'];
            
            if (isset($sort['column']) && in_array($sort['column'], $valid_columns) &&
                isset($sort['direction']) && in_array(strtoupper($sort['direction']), $valid_directions)) {
                $query .= " ORDER BY " . $sort['column'] . " " . strtoupper($sort['direction']);
            }
        } else {
            $query .= " ORDER BY s.created_at DESC";
        }

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT s.*, 
                        u.name as creator_name,
                        COUNT(DISTINCT c.comment_id) as comment_count
                 FROM " . $this->table_name . " s
                 LEFT JOIN users u ON s.created_by = u.username
                 LEFT JOIN suggestion_comments c ON s.suggestion_id = c.suggestion_id
                 WHERE s.suggestion_id = :id
                 GROUP BY s.suggestion_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $allowed_fields = ['title', 'description', 'category', 'location', 'status', 'updated_by'];
        $fields = array();
        $values = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed_fields)) {
                $fields[] = $key . " = :" . $key;
                $values[':' . $key] = $key === 'status' ? $value : htmlspecialchars(strip_tags($value));
            }
        }

        if (empty($fields)) {
            return false;
        }

        $values[':id'] = $id;
        $values[':updated_at'] = date('Y-m-d H:i:s');

        $query = "UPDATE " . $this->table_name . "
                 SET " . implode(", ", $fields) . ", updated_at = :updated_at
                 WHERE suggestion_id = :id";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($values);
    }

    public function vote($suggestion_id, $user_id, $vote_type) {
        $this->conn->beginTransaction();

        try {
            // Check if user has already voted
            $check_query = "SELECT vote_type FROM suggestion_votes 
                          WHERE suggestion_id = :suggestion_id AND user_id = :user_id";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->bindParam(":suggestion_id", $suggestion_id);
            $check_stmt->bindParam(":user_id", $user_id);
            $check_stmt->execute();
            $existing_vote = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_vote) {
                if ($existing_vote['vote_type'] === $vote_type) {
                    // Remove vote if clicking same button
                    $delete_query = "DELETE FROM suggestion_votes 
                                   WHERE suggestion_id = :suggestion_id AND user_id = :user_id";
                    $delete_stmt = $this->conn->prepare($delete_query);
                    $delete_stmt->bindParam(":suggestion_id", $suggestion_id);
                    $delete_stmt->bindParam(":user_id", $user_id);
                    $delete_stmt->execute();

                    // Update vote count
                    $vote_change = $vote_type === 'upvote' ? -1 : 1;
                } else {
                    // Change vote type
                    $update_query = "UPDATE suggestion_votes 
                                   SET vote_type = :vote_type 
                                   WHERE suggestion_id = :suggestion_id AND user_id = :user_id";
                    $update_stmt = $this->conn->prepare($update_query);
                    $update_stmt->bindParam(":suggestion_id", $suggestion_id);
                    $update_stmt->bindParam(":user_id", $user_id);
                    $update_stmt->bindParam(":vote_type", $vote_type);
                    $update_stmt->execute();

                    // Update vote count
                    $vote_change = $vote_type === 'upvote' ? 2 : -2;
                }
            } else {
                // Add new vote
                $insert_query = "INSERT INTO suggestion_votes 
                               (suggestion_id, user_id, vote_type) 
                               VALUES (:suggestion_id, :user_id, :vote_type)";
                $insert_stmt = $this->conn->prepare($insert_query);
                $insert_stmt->bindParam(":suggestion_id", $suggestion_id);
                $insert_stmt->bindParam(":user_id", $user_id);
                $insert_stmt->bindParam(":vote_type", $vote_type);
                $insert_stmt->execute();

                // Update vote count
                $vote_change = $vote_type === 'upvote' ? 1 : -1;
            }

            // Update suggestion vote count
            $update_votes_query = "UPDATE " . $this->table_name . "
                                 SET votes = votes + :vote_change
                                 WHERE suggestion_id = :suggestion_id";
            $update_votes_stmt = $this->conn->prepare($update_votes_query);
            $update_votes_stmt->bindParam(":suggestion_id", $suggestion_id);
            $update_votes_stmt->bindParam(":vote_change", $vote_change);
            $update_votes_stmt->execute();

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getUserVote($suggestion_id, $user_id) {
        $query = "SELECT vote_type FROM suggestion_votes 
                 WHERE suggestion_id = :suggestion_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":suggestion_id", $suggestion_id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['vote_type'] : null;
    }

    public function addComment($data) {
        $query = "INSERT INTO suggestion_comments 
                (suggestion_id, user_id, comment, parent_id) 
                VALUES (:suggestion_id, :user_id, :comment, :parent_id)";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $stmt->bindParam(":suggestion_id", $data['suggestion_id']);
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":comment", htmlspecialchars(strip_tags($data['comment'])));
        $stmt->bindParam(":parent_id", $data['parent_id']);

        return $stmt->execute();
    }

    public function getComments($suggestion_id) {
        $query = "SELECT c.*, u.name as user_name
                 FROM suggestion_comments c
                 LEFT JOIN users u ON c.user_id = u.username
                 WHERE c.suggestion_id = :suggestion_id
                 ORDER BY c.created_at ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":suggestion_id", $suggestion_id);
        $stmt->execute();

        return $stmt;
    }

    public function getTopSuggestions($limit = 5) {
        $query = "SELECT s.*, 
                        u.name as creator_name,
                        COUNT(DISTINCT c.comment_id) as comment_count
                 FROM " . $this->table_name . " s
                 LEFT JOIN users u ON s.created_by = u.username
                 LEFT JOIN suggestion_comments c ON s.suggestion_id = c.suggestion_id
                 WHERE s.status != 'rejected'
                 GROUP BY s.suggestion_id
                 ORDER BY s.votes DESC, s.created_at DESC
                 LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    public function getStats() {
        $query = "SELECT 
                    COUNT(*) as total_suggestions,
                    SUM(CASE WHEN status = 'implemented' THEN 1 ELSE 0 END) as implemented_count,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    COUNT(DISTINCT created_by) as unique_contributors
                 FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
