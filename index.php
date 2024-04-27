<?php
include 'functions.php';
$db = new Database();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Buku Telepon</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-fluid">
        <h1 class="mb-4">List Buku Telepon</h1>
        <div class="row">
            <div class="col-md-6 mb-3">
                <button class="btn btn-primary" id="addDataBtn" data-toggle="modal" data-target="#addDataModal">Add Data</button>
            </div>
            <div class="col-md-6 mb-3 d-flex justify-content-end">
                <div class="mr-2">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search data...">
                </div>
                <div>
                    <button class="btn btn-primary" id="searchBtn">Search</button>
                </div>
            </div>
        </div>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nama</th> 
                    <th>NIM</th>
                    <th>No Telp</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="dataTableBody">
            </tbody>
        </table>
    </div>

    <!-- Add Data Modal -->
    <div class="modal fade" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="addDataModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDataModalLabel">Add Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="dataForm" enctype="multipart/form-data">
                        <input type="hidden" id="id" name="id">
                        <div class="form-group">
                            <label for="Name">Name</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="Nim">NIM</label>
                            <input type="text" class="form-control" id="nim" name="nim" required>
                        </div>
                        <div class="form-group">
                            <label for="Notel">No Telp</label>
                            <input type="tel" class="form-control" id="notel" name="notel" required>
                        </div>
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveDataBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
$(document).ready(function () {
    function loadData() {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: { action: 'load' },
            success: function (response) {
                $('tbody').html(response);
            }
        });
    }

    $('#addDataBtn').click(function () {
        $('#addDatatModal').modal('show');
        $('#dataForm')[0].reset();
        $('#dataForm').attr('action', 'add');
        $('#addDataModalLabel').text('Add Data');
    });

    // Add or Update function
    $('#saveDataBtn').click(function () {
        var formData = new FormData($('#dataForm')[0]);
        var action = $('#dataForm').attr('action');
        formData.append('action', action);

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response === 'success') {
                    var successMessage = (action === "add") ? 'Data added successfully!' : 'Data updated successfully!';
                    Swal.fire({
                        icon: 'success',
                        title: successMessage,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#addDataModal').modal('hide');
                    loadData();
                } else {
                    var errorMessage = (action === "add") ? 'Failed to add data' : 'Failed to update data';
                    Swal.fire({
                        icon: 'error',
                        title: errorMessage,
                        text: 'Please try again later.',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            }
        });
    });

    // Delete function
    $(document).on('click', '.delete-data', function () {
        var Id = $(this).data('id');
        if (confirm('Are you sure you want to delete this data?')) {
            $.ajax({
                url: 'ajax.php',
                type: 'POST',
                data: { action: 'delete', id: Id },
                success: function (response) {
                    if (response === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Data deleted successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        loadData();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to delete data',
                            text: 'Please try again later.',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                }
            });
        }
    });

    // Get data for modal
    $(document).on('click', '.update-data', function () {
        var Id = $(this).data('id');
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: { action: 'get_data', id: Id },
            success: function (response) {
                var dataDetails = JSON.parse(response);
                
                $('#id').val(dataDetails.id);
                $('#nama').val(dataDetails.nama);
                $('#nim').val(dataDetails.nim);
                $('#notel').val(dataDetails.notel);
                $('#email').val(dataDetails.email);

                $('#dataForm').attr('action', 'update');
                $('#addDataModal').modal('show');
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error occurred while get data',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // Search function
    $(document).on('click', '#searchBtn', function () {
        var query = $('#searchInput').val();

        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: {
                action: 'search',
                query: query
            },
            success: function (response) {
                $('tbody').html(response);
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error occurred while searching',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // initiate for data loaded
    loadData();
});
</script>
</body>
</html>
