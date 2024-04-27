<?php
class Database {
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "soal2";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function readData() {
        $query = "SELECT * FROM buku_telepon";
        
        try {
            $result = $this->conn->query($query);
            if ($result->num_rows > 0) {
                $data = array();
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                return $data;
            } else {
                return array();
            }
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function addEntry($nama, $nim, $noTel, $email) {
        $nama = $this->conn->real_escape_string($nama);
        $nim = $this->conn->real_escape_string($nim);
        $noTel = $this->conn->real_escape_string($noTel);
        $email = $this->conn->real_escape_string($email);

        $query = "INSERT INTO buku_telepon (nama, nim, notel, email) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssss", $nama, $nim, $noTel, $email);
        if ($stmt->execute()) {
            return true;
        } else {
            return "Error adding entry: " . $stmt->error;
        }
    }

    public function getEntryById($entryId) {
        $query = "SELECT * FROM buku_telepon WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $entryId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function deleteEntry($entryId) {
        $query = "DELETE FROM buku_telepon WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $entryId);
        if ($stmt->execute()) {
            return true;
        } else {
            return "Error deleting entry: " . $stmt->error;
        }
    }

    public function updateEntry($entryId, $nama, $nim, $noTel, $email) {
        $nama = $this->conn->real_escape_string($nama);
        $nim = $this->conn->real_escape_string($nim);
        $noTel = $this->conn->real_escape_string($noTel);
        $email = $this->conn->real_escape_string($email);

        $query = "UPDATE buku_telepon SET nama = ?, nim = ?, notel = ?, email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $nama, $nim, $noTel, $email, $entryId);
        if ($stmt->execute()) {
            return true;
        } else {
            return "Error updating entry: " . $stmt->error;
        }
    }

    public function searchEntry($query) {
        $query = $this->conn->real_escape_string($query);
        $searchQuery = "SELECT * FROM buku_telepon WHERE nama LIKE '%$query%' OR nim LIKE '%$query%' OR notel LIKE '%$query%' OR email LIKE '%$query%'";
        $result = $this->conn->query($searchQuery);
        if ($result->num_rows > 0) {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } else {
            return array();
        }
    }
}
?>
