<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    require_once "functions.php";

    $db = new Database();
    $action = $_POST["action"];
    if ($action == "load") {
        $datas = $db->readData();
        if (!empty($datas)) {
            foreach ($datas as $data) {
                echo "<tr id='" . $data['id'] . "'>";
                echo "<td>" . $data['nama'] . "</td>";
                echo "<td>" . $data['nim'] . "</td>";
                echo "<td>" . $data['notel'] . "</td>";
                echo "<td>" . $data['email'] . "</td>";
                echo "<td><div class='d-flex'>";
                echo "<button class='btn btn-info update-data mr-2' data-id='" . $data['id'] . "'>Update</button>";
                echo "<button class='btn btn-danger delete-data' data-id='" . $data['id'] . "'>Delete</button>";
                echo "</div></td>";
                echo "</tr>";
            }
        } else {
            echo '<tr><td colspan="7">No Data found.</td></tr>';
        }
    } elseif ($action == "add" || $action === "update") {
        $nama = $_POST["nama"];
        $nim = $_POST["nim"];
        $notel = $_POST["notel"];
        $email = $_POST["email"];

        if ($action === 'add') {
            $result = $db->addEntry($nama, $nim, $notel, $email);
        } elseif ($action === 'update' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $result = $db->updateEntry($id, $nama, $nim, $notel, $email);
        }
        
        if ($result) {
            echo "success";
        } else {
            error_log("Failed to add data: " . $result);
    
            echo "error: " . $result;
        }
    } elseif ($action == "delete") {
        $id = $_POST['id'];
        $result = $db->deleteEntry($id);
        if ($result) {
            echo "success";
        } else {
            error_log("Failed to delete data: " . $db->getErrorMessage());
    
            echo "error: " . $db->getErrorMessage();
        }
    } elseif ($action == "search") {
        $query = $_POST["query"];
        $filteredDatas = $db->searchEntry($query);
        if (!empty($filteredDatas)) {
            foreach ($filteredDatas as $data) {
                echo "<tr id='" . $data['id'] . "'>";
                echo "<td>" . $data['nama'] . "</td>";
                echo "<td>" . $data['nim'] . "</td>";
                echo "<td>" . $data['notel'] . "</td>";
                echo "<td>" . $data['email'] . "</td>";
                echo "<td><div class='d-flex'>";
                echo "<button class='btn btn-info update-data mr-2' data-id='" . $data['id'] . "'>Update</button>";
                echo "<button class='btn btn-danger delete-data' data-id='" . $data['id'] . "'>Delete</button>";
                echo "</div></td>";
                echo "</tr>";
            }
        } else {
            echo '<tr><td colspan="7">No data found.</td></tr>';
        }
    } elseif ($action === 'get_data' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $data = $db->getEntryById($id);
        if ($data) {
            echo json_encode($data);
        } else {
            echo "error: data not found";
        }
    }
} else {
    header("Location: index.php");
    exit;
}
?>
