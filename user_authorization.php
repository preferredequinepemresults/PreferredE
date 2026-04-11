<?php
include("./header.php");
include("./session_page.php");
require_once("config.php");
require_once("db-settings.php");
require_once("cognito.php");

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle Unauthorize
    if (isset($_POST['USER_ID_UNAUTH']) && !empty($_POST['USER_ID_UNAUTH'])) {
        $result = unauthorizeUser($_POST['USER_ID_UNAUTH']);
        echo "<script>
            alert('" . $result . "');
            window.location.href='user_authorization.php';
        </script>";
        exit();
    }
    
    // Handle Authorize
    if (isset($_POST['USER_ID_AUTH']) && !empty($_POST['USER_ID_AUTH'])) {
        $result = authorizeUser($_POST['USER_ID_AUTH']);
        echo "<script>
            alert('" . $result . "');
            window.location.href='user_authorization.php';
        </script>";
        exit();
    }
    
    // Handle Delete User (from both database and Cognito)
    if (isset($_POST['USER_ID_DELETE']) && !empty($_POST['USER_ID_DELETE'])) {
        $user_id = $_POST['USER_ID_DELETE'];
        $user_email = $_POST['USER_EMAIL'] ?? '';
        
        $result = deleteUserCompletely($user_id, $user_email);
        echo "<script>
            alert('" . $result . "');
            window.location.href='user_authorization.php';
        </script>";
        exit();
    }
}

// Get user data
$resultFound = getUserData();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authorization | Preferred Equine</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .auth-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Header Section */
        .auth-header {
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            font-size: 1.875rem;
            font-weight: 600;
            color: #0f172a;
            letter-spacing: -0.025em;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.25rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #0f172a;
        }

        .stat-desc {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        /* Table Container */
        .table-container {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Custom Table Styles */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .modern-table thead tr {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table th {
            padding: 1rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s;
        }

        .modern-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .modern-table td {
            padding: 1rem 1rem;
            color: #334155;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1.5;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }

        .btn-icon i {
            font-size: 0.875rem;
        }

        .btn-auth {
            background: #f0fdf4;
            color: #166534;
            border-color: #86efac;
        }

        .btn-auth:hover {
            background: #dcfce7;
        }

        .btn-unauth {
            background: #fffbeb;
            color: #854d0e;
            border-color: #fde047;
        }

        .btn-unauth:hover {
            background: #fef3c7;
        }

        .btn-delete {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .btn-delete:hover {
            background: #fee2e2;
        }

        /* User Email Cell */
        .user-email {
            font-size: 0.8125rem;
            color: #64748b;
            margin-top: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .auth-container {
                padding: 0 1rem;
            }

            .stats-cards {
                grid-template-columns: 1fr;
            }

            .modern-table {
                font-size: 0.8125rem;
            }

            .modern-table th,
            .modern-table td {
                padding: 0.75rem;
            }

            .action-group {
                flex-direction: column;
            }

            .btn-icon {
                width: 100%;
                justify-content: center;
            }
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: white;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            transform: translateY(150%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .toast.show {
            transform: translateY(0);
        }

        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toast-success {
            border-left: 4px solid #22c55e;
        }

        .toast-error {
            border-left: 4px solid #ef4444;
        }

        code {
            background: #f1f5f9;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-family: 'Inter', monospace;
            font-size: 0.8125rem;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Header -->
        <div class="auth-header">
            <h1>User Authorization</h1>
            <p>Manage user access and permissions across the platform</p>
        </div>

        <!-- Stats Cards -->
        <?php
        $total_users = count($resultFound);
        $active_users = 0;
        $inactive_users = 0;

        // Calculate stats
        foreach ($resultFound as $row) {
            // Check active status
            if (isset($row['ACTIVE']) && $row['ACTIVE'] == 'Y') {
                $active_users++;
            } else {
                $inactive_users++;
            }
        }
        ?>

        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-value"><?php echo $total_users; ?></div>
                <div class="stat-desc">All registered users</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active</div>
                <div class="stat-value"><?php echo $active_users; ?></div>
                <div class="stat-desc">Can access the system</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Inactive</div>
                <div class="stat-value"><?php echo $inactive_users; ?></div>
                <div class="stat-desc">Cannot access system</div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="modern-table" id="userTable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $number = 0;
                    foreach ($resultFound as $row) {
                        $number++;
                        $user_id = $row['USER_ID'] ?? '';
                        $user_email = $row['EMAIL'] ?? '';
                        $username = $row['USERNAME'] ?? '';
                        $full_name = trim(($row['FNAME'] ?? '') . ' ' . ($row['LNAME'] ?? ''));
                        $full_name = $full_name ?: $username;
                        
                        $active_status = ($row['ACTIVE'] ?? 'N') == 'Y' ? 'active' : 'inactive';
                        
                        $role = $row['USERROLE'] ?? 'user';
                        $role_names = ['A' => 'Admin', 'T' => 'Thoroughbred', 'S' => 'Standardbred', 'ST' => 'Full Access', 'user' => 'User'];
                        $role_display = $role_names[$role] ?? $role;
                    ?>
                    <tr>
                        <td><?php echo $number; ?></td>
                        <td><code><?php echo htmlspecialchars($user_id); ?></code></td>
                        <td>
                            <div style="font-weight: 500;"><?php echo htmlspecialchars($full_name); ?></div>
                        </td>
                        <td><?php echo htmlspecialchars($user_email); ?></td>
                        <td><?php echo htmlspecialchars($username); ?></td>
                        <td>
                            <span class="badge <?php echo $active_status === 'active' ? 'badge-active' : 'badge-inactive'; ?>">
                                <i class="fas fa-<?php echo $active_status === 'active' ? 'check-circle' : 'times-circle'; ?>" style="margin-right: 0.375rem;"></i>
                                <?php echo $active_status === 'active' ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td>
                            <span style="background: #f1f5f9; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">
                                <?php echo $role_display; ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <form method="POST" style="display: inline;" onsubmit="return confirmAction('authorize', '<?php echo $user_id; ?>')">
                                    <input type="hidden" name="USER_ID_AUTH" value="<?php echo $user_id; ?>">
                                    <button type="submit" class="btn-icon btn-auth" title="Authorize User">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Authorize</span>
                                    </button>
                                </form>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirmAction('unauthorize', '<?php echo $user_id; ?>')">
                                    <input type="hidden" name="USER_ID_UNAUTH" value="<?php echo $user_id; ?>">
                                    <button type="submit" class="btn-icon btn-unauth" title="Unauthorize User">
                                        <i class="fas fa-ban"></i>
                                        <span>Unauthorize</span>
                                    </button>
                                </form>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirmDelete('<?php echo $user_id; ?>', '<?php echo htmlspecialchars($user_email); ?>')">
                                    <input type="hidden" name="USER_ID_DELETE" value="<?php echo $user_id; ?>">
                                    <input type="hidden" name="USER_EMAIL" value="<?php echo htmlspecialchars($user_email); ?>">
                                    <button type="submit" class="btn-icon btn-delete" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <div class="toast-content" id="toastContent">
            <i id="toastIcon" class="fas"></i>
            <span id="toastMessage"></span>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#userTable').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[0, 'asc']],
                language: {
                    search: "<i class='fas fa-search' style='margin-right: 0.5rem;'></i>Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "→",
                        previous: "←"
                    }
                },
                initComplete: function() {
                    $('.dataTables_filter input').attr('placeholder', 'Search users...');
                }
            });
        });

        // Confirmation dialogs
        function confirmAction(action, userId) {
            const messages = {
                'authorize': 'authorize',
                'unauthorize': 'unauthorize'
            };
            return confirm(`Are you sure you want to ${messages[action]} user: ${userId}?`);
        }

        function confirmDelete(userId, email) {
            return confirm(`⚠️ WARNING: Are you sure you want to PERMANENTLY DELETE user ${userId} (${email})?\n\nThis will delete the user from BOTH the database AND Cognito. This action CANNOT be undone!`);
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toastIcon');
            const messageEl = document.getElementById('toastMessage');
            
            toast.className = 'toast show toast-' + type;
            
            let iconName = 'info-circle';
            if (type === 'success') iconName = 'check-circle';
            if (type === 'error') iconName = 'exclamation-circle';
            
            icon.className = 'fas fa-' + iconName;
            messageEl.textContent = message;
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Check for URL parameters for notifications
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('deleted')) {
            showToast('User deleted successfully', 'success');
        } else if (urlParams.has('error')) {
            showToast('An error occurred', 'error');
        }
    </script>
</body>

<?php
// Function to completely delete user from both database and Cognito
function deleteUserCompletely($user_id, $user_email) {
    global $mysqli;
    
    // Start transaction
    $mysqli->begin_transaction();
    
    try {
        // First, get user details if email not provided
        if (empty($user_email)) {
            $stmt = $mysqli->prepare("SELECT EMAIL FROM users WHERE USER_ID = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $user_email = $user['EMAIL'] ?? '';
            $stmt->close();
        }
        
        // 1. Delete from Cognito if configured
        $cognito_message = "";
        
        if (!empty($user_email) && defined('COGNITO_REGION') && defined('COGNITO_USER_POOL_ID') && 
            COGNITO_REGION !== 'us-east-1' && COGNITO_USER_POOL_ID !== 'your-user-pool-id') {
            try {
                require_once 'vendor/autoload.php';
                
                $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                    'region' => COGNITO_REGION,
                    'version' => 'latest'
                ]);
                
                $client->adminDeleteUser([
                    'UserPoolId' => COGNITO_USER_POOL_ID,
                    'Username' => $user_email
                ]);
                
                error_log("Cognito user deleted by admin: $user_email");
                $cognito_message = " and Cognito";
                
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                error_log("Cognito delete failed for user {$user_email}: " . $error_message);
                
                if (strpos($error_message, 'UserNotFoundException') !== false) {
                    $cognito_message = " (Cognito user not found)";
                } else {
                    // Don't throw, just log - we still want to delete from database
                    error_log("Continuing with database deletion despite Cognito error");
                }
            }
        }
        
        // 2. Delete from database
        $delete_stmt = $mysqli->prepare("DELETE FROM users WHERE USER_ID = ?");
        $delete_stmt->bind_param("s", $user_id);
        
        if (!$delete_stmt->execute()) {
            throw new Exception("Database delete failed: " . $delete_stmt->error);
        }
        
        $affected_rows = $delete_stmt->affected_rows;
        $delete_stmt->close();
        
        if ($affected_rows === 0) {
            throw new Exception("User not found in database");
        }
        
        // Commit transaction
        $mysqli->commit();
        
        return "User $user_id successfully deleted from database$cognito_message!";
        
    } catch (Exception $e) {
        $mysqli->rollback();
        error_log("User deletion failed for ID {$user_id}: " . $e->getMessage());
        return "Error deleting user: " . $e->getMessage();
    }
}
?>
</html>