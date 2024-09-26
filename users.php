<?php include ('include/connect.php')?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include ('include/nav.php')?> 

    <div class="container mt-5">
        <h2>User Management</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                // Fetch users
                $sql = "SELECT user_id, username, email, role, status FROM users";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['user_id']}</td>
                            <td>{$row['username']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['role']}</td>
                            <td>{$row['status']}</td>
                            <td>
                                <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#blockModal' 
                                        data-userid='{$row['user_id']}' data-username='{$row['username']}' 
                                        data-status='{$row['status']}'>
                                        " . ($row['status'] == 'active' ? 'Block' : 'Unblock') . "
                                </button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No users found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="blockModal" tabindex="-1" aria-labelledby="blockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blockModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to <span id="actionText">block</span> <span id="userName"></span>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="block_user.php" method="POST">
                        <input type="hidden" name="user_id" id="modalUserId">
                        <input type="hidden" name="action" id="modalAction">
                        <button type="submit" class="btn btn-danger" id="confirmActionButton">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const blockModal = document.getElementById('blockModal');
        blockModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-userid');
            const userName = button.getAttribute('data-username');
            const userStatus = button.getAttribute('data-status');

            const modalUserId = blockModal.querySelector('#modalUserId');
            const modalAction = blockModal.querySelector('#modalAction');
            const actionText = blockModal.querySelector('#actionText');
            const userNameSpan = blockModal.querySelector('#userName');
            const confirmButton = blockModal.querySelector('#confirmActionButton');

            modalUserId.value = userId;
            modalAction.value = (userStatus === 'active') ? 'block' : 'unblock';
            actionText.textContent = (userStatus === 'active') ? 'block' : 'unblock';
            userNameSpan.textContent = userName;
            confirmButton.textContent = (userStatus === 'active') ? 'Block User' : 'Unblock User';
        });
    </script>
</body>
</html>
