<?php
session_start();
// CHECK IF LOGGED IN ELSE REDIRECT BACK TO LOGIN PAGE
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee</title>
    <!-- STYLES -->
    <link rel="stylesheet" href="assets/styles/root.css">
    <link rel="stylesheet" href="assets/styles/admin_users.css">
    <link rel="stylesheet" href="assets/styles/sidebar.css">
    <!-- ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    include 'components/admin_sidebar.php';
    include 'db/db_connection.php';
    // PAGENATION LOGIC
    $limit = 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    // FETCH OF TOTAL NUMBER OF USERS
    $totalQuery = "SELECT COUNT(*) as total FROM users WHERE position = 'Intern'";
    $totalResult = $conn->query($totalQuery);
    $totalUsers = $totalResult->fetch_assoc()['total'];
    $totalPages = ceil($totalUsers / $limit);
    // FETCH USERS OF CURRENT PAGE
    $deptFilter = isset($_GET['dept']) ? $_GET['dept'] : 'All';
    $searchFilter = isset($_GET['search']) ? $_GET['search'] : '';
    $query = "SELECT id, pfp, fullname, username, email, department, school FROM users WHERE position = 'Intern'";
    if ($deptFilter !== 'All') {
        $query .= " AND department = ?";
    }
    if (!empty($searchFilter)) {
        $query .= " AND fullname LIKE ?";
    }
    $query .= " LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    if ($deptFilter !== 'All' && !empty($searchFilter)) {
        $searchParam = '%' . $searchFilter . '%';
        $stmt->bind_param('ssii', $deptFilter, $searchParam, $limit, $offset);
    } elseif ($deptFilter !== 'All') {
        $stmt->bind_param('sii', $deptFilter, $limit, $offset);
    } elseif (!empty($searchFilter)) {
        $searchParam = '%' . $searchFilter . '%';
        $stmt->bind_param('sii', $searchParam, $limit, $offset);
    } else {
        $stmt->bind_param('ii', $limit, $offset);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    ?>
    <div class="main-content">
        <h1>Administration</h1>
        <p>Manage information of all users.</p>
        <div class="header-container">
            <div class="search-section">
                <div class="search-bar">
                    <i class="fa fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search name..."
                        value="<?= htmlspecialchars($searchFilter, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <button class="search-btn" id="searchBtn">Search</button>
                <button class="refresh-btn"><i class="fa fa-refresh"></i></button>
            </div>
            <div class="button-group">
                <?php if ($_SESSION['position'] == 'Admin'): ?>
                    <div class="status-dropdown-wrapper">
                        <select class="status-dropdown" id="deptFilter">
                            <option value="All" <?= $deptFilter === 'All' ? 'selected' : '' ?>>All Departments</option>
                            <option value="Admin" <?= $deptFilter === 'Admin' ? 'selected' : '' ?>>Admin Department</option>
                            <option value="IT" <?= $deptFilter === 'IT' ? 'selected' : '' ?>>IT Department</option>
                            <option value="HR" <?= $deptFilter === 'HR' ? 'selected' : '' ?>>HR Department</option>
                            <option value="Marketing" <?= $deptFilter === 'Marketing' ? 'selected' : '' ?>>Marketing Department
                            </option>
                        </select>
                    </div>
                <?php endif; ?>
                <button class="create-user-btn" id="createUserBtn">
                    <i class="fa-solid fa-user"></i> New User
                </button>
            </div>
        </div>
        <!-- TABLE DISPLAYING INTERNS -->
        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pfp</th>
                            <th>Fullname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>School</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><img src="<?= htmlspecialchars($user['pfp'], ENT_QUOTES, 'UTF-8') ?>" alt="Profile Picture"
                                        width="50" height="50"></td>
                                <td><?= htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['department'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['school'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <button class="view-user-btn" data-id="<?= $user['id'] ?>"><i
                                            class="fa fa-folder"></i></button>
                                    <button class="delete-user-btn" data-id="<?= $user['id'] ?>"><i
                                            class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <h2 class="no-data"><i class="fa-solid fa-folder-open"></i> No data found</h2>
            <?php endif; ?>
        </div>
        <!-- PAGINATION -->
        <div class="pagination">
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&dept=<?= $deptFilter ?>&search=<?= $searchFilter ?>"
                            class="<?= ($i == $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- ADMIN CREATE ACCOUNT MODAL -->
        <div id="createUserModal" class="modal-overlay">
            <div class="modal-container">
                <h2>Create an account</h2>
                <p>Fill in your details below.</p>
                <form class="register-form" id="registerForm">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <span class="error-message" id="email-user-error"></span>
                    <input type="text" name="school" placeholder="School" required>
                    <select name="position" required>
                        <option value="">Select Position</option>
                        <option value="Supervisor">Supervisor</option>
                        <option value="Intern">Intern</option>
                    </select>
                    <select name="department" required>
                        <option value="">Select Department</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Admin">Admin</option>
                        <option value="IT">IT</option>
                        <option value="HR">HR</option>
                    </select>
                    <span class="error-message" id="pass-error"></span>
                    <input type="file" id="profileImage" name="profileImage" accept="image/*">
                    <div class="modal-buttons">
                        <button type="submit" name="register" id="register-btn">Create</button>
                        <button type="button" onclick="closeModal('createUserModal')">Close</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- USER DETAILS MODAL -->
        <div id="userDetailsModal" class="modal-overlay">
            <div class="modal-container">
                <div class="user-info">
                    <div class="profile-container">
                        <img class="profile-pic">
                        <div class="profile-info">
                            <h2 id="fullname"></h2>
                            <p id="username">@</p>
                        </div>
                    </div>
                    <div class="other-details">
                        <p><strong>Email: </strong><span class="email"></span></p>
                        <p><strong>School: </strong><span class="school"></span></p>
                        <p><strong>Department: </strong><span class="department"></span> Department</p>
                    </div>
                </div>
                <div class="modal-buttons">
                    <button type="button" onclick="closeModal('userDetailsModal')">Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Handle department filter change
            const deptFilter = document.getElementById('deptFilter');
            if (deptFilter) {
                deptFilter.addEventListener('change', function () {
                    const selectedDept = this.value;
                    const searchInput = document.getElementById('searchInput').value;
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('dept', selectedDept);
                    currentUrl.searchParams.set('search', searchInput);
                    window.location.href = currentUrl.toString();
                });
            }

            // Handle search button click
            const searchBtn = document.getElementById('searchBtn');
            if (searchBtn) {
                searchBtn.addEventListener('click', function () {
                    const searchInput = document.getElementById('searchInput').value;
                    const selectedDept = deptFilter ? deptFilter.value : 'All';
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('dept', selectedDept);
                    currentUrl.searchParams.set('search', searchInput);
                    window.location.href = currentUrl.toString();
                });
            }

            // Handle refresh button click
            const refreshBtn = document.querySelector('.refresh-btn');
            if (refreshBtn) {
                refreshBtn.addEventListener('click', function () {
                    window.location.href = 'admin_users.php';
                });
            }

            // Handle create user button click
            const createUserBtn = document.getElementById('createUserBtn');
            createUserBtn.addEventListener('click', function () {
                document.getElementById('createUserModal').style.display = 'flex';
            });

            // Handle form submission
            document.getElementById('registerForm').addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(this);

                fetch('db/db_create-user.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success!', 'User account has been created.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    })
                    .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
            });

            // Handle view user details button
            document.querySelectorAll('.view-user-btn').forEach(button => {
                button.addEventListener('click', function () {
                    document.getElementById('userDetailsModal').style.display = 'flex';
                })
            })

            // Handle view user details button
            document.querySelectorAll('.view-user-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = this.getAttribute('data-id');
                    fetch('db/db_fetch-user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `user_id=${userId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const user = data.user;
                                document.getElementById('fullname').textContent = user.fullname;
                                document.getElementById('username').textContent = "@" + user.username;
                                document.querySelector('.email').textContent = user.email;
                                document.querySelector('.school').textContent = user.school;
                                document.querySelector('.department').textContent = user.department;
                                document.querySelector('#userDetailsModal img').src = user.pfp;
                                document.getElementById('userDetailsModal').style.display = 'flex';
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
                });
            });

            // Handle delete user button click
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const userId = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Delete User?',
                        text: 'Are you sure you want to delete this user?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('db/db_delete-user.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: `user_id=${userId}`
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Deleted!', 'User has been deleted.', 'success')
                                            .then(() => location.reload());
                                    } else {
                                        Swal.fire('Error!', 'Failed to delete user.', 'error');
                                    }
                                })
                                .catch(error => Swal.fire('Error!', 'Something went wrong.', 'error'));
                        }
                    });
                });
            });
        });

        // CLOSE MODAL THAT IS CURRENTLY OPENED
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>
</body>

</html>