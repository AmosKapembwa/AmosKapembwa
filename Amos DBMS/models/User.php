<?php
class User {
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $address;
    public $phone;
    public $role;
    public $created_at;
    public $last_login;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (username, email, password_hash, first_name, last_name, address, phone, role)
                VALUES
                (:username, :email, :password, :first_name, :last_name, :address, :phone, :role)";

        $stmt = $this->conn->prepare($query);

        // Sanitize and hash password
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->role = htmlspecialchars(strip_tags($this->role));

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":first_name", $this->first_name);
        $stmt->bindParam(":last_name", $this->last_name);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":role", $this->role);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login($username, $password) {
        $query = "SELECT user_id, username, password_hash, role FROM " . $this->table_name . " 
                 WHERE username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(password_verify($password, $row['password_hash'])) {
                // Update last login
                $update_query = "UPDATE " . $this->table_name . " 
                               SET last_login = CURRENT_TIMESTAMP 
                               WHERE user_id = :user_id";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(":user_id", $row['user_id']);
                $update_stmt->execute();

                return [
                    'user_id' => $row['user_id'],
                    'username' => $row['username'],
                    'role' => $row['role']
                ];
            }
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function usernameExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $this->username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
