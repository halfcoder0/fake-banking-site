<?php
include('../controllers/admin_controller.php');
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');

$nonce = generate_random();
add_csp_header($nonce);
$_SESSION[SessionVariables::NONCE->value] = $nonce;

try {
    $auth_controller = new AuthController();
    $auth_controller->check_user_role([Roles::ADMIN]);
} catch (Exception $exception) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Error with page";
    error_log($exception->getMessage() . $exception->getTraceAsString());
} catch (Throwable $throwable) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Error with page";
    error_log($throwable->getMessage() . $throwable->getTraceAsString());
}
?>

<!DOCTYPE html>
<html lang="en">

<?php
include('../includes/admin_header.php');
?>

<style nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
.username-clickable {
  cursor: pointer;
  font-weight: bold;
  text-decoration: underline;
  color: white;
  transition: color 0.2s ease;
}

.username-clickable:hover {
  color: #66ccff;
}

.expandable-row {
  display: none; /* hidden on page load */
  background-color: #1e1e1e; /* optional for contrast */
  color: #ddd;
}
</style>

<style nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
.user-details-row {
  display: none ; /* Force hidden on page load */
  background-color: #2c2c2c;
}
</style>

<body>
    <?php

    // Store all search results if not already
    if (!empty($_SESSION['search_results'])) {
        $all_users = $_SESSION['search_results'];
    } else {
        $all_users = []; // default empty
    }

    // Users per page
    $users_per_page = 10;

    // Check if a page navigation POST request was made
    if (isset($_POST['page'])) {
        $_SESSION['current_page'] = (int)$_POST['page'];
    }

    // Default current page
    $current_page = $_SESSION['current_page'] ?? 1;

    // Calculate total pages
    $total_users = count($all_users);
    $total_pages = ceil($total_users / $users_per_page);

    // Get current page slice
    $start_index = ($current_page - 1) * $users_per_page;
    $search_results = array_slice($all_users, $start_index, $users_per_page);
    ?>
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <?php
        include('../includes/admin_topbar.php');
        include('../includes/admin_left_navbar.php');
        ?>
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <?php
            include('../includes/admin_dashboard_header.php');
            ?>
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center m-b-30">Search Staff</h3>
                                <!-- Centered Search Form -->
                                <form class="form-inline justify-content-center mb-4" method="POST" action="/admin_controller">
                                    <!-- Hidden action -->
                                    <input type="hidden" name="action" value="search_staff">
                                    <div class="form-group mr-2">
                                        <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Enter username">
                                    </div>
                                    <button type="submit" class="btn btn-info btn-lg">Search</button>
                                </form>
                                <div class="card">
                                    <div class="card-body">
                                        <?php if (!empty($search_results)): ?>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-hover">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Username</th>
                                                                    <th>Role</th>
                                                                    <th>Last Login</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($search_results as $user): ?>
                                                                    <tr class="user-row">
                                                                        <td class="username-clickable" style="cursor:pointer; font-weight:bold; text-decoration:underline; color:#00bfff;" data-userid="<?= $user['Username'] ?>">
                                                                            <?= htmlspecialchars($user['Username']) ?>
                                                                        </td>
                                                                        <td><?= htmlspecialchars($user['Role']) ?></td>
                                                                        <td><?= htmlspecialchars($user['LastLogin']) ?></td>
                                                                        <td>
                                                                            <form method="POST" action="/admin_controller" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                                                <input type="hidden" name="action" value="delete_staff">
                                                                                <input type="hidden" name="userid" value="<?= $user['UserID'] ?>">
                                                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="user-details-row" style="display:none; background-color:#2c2c2c;">
                                                                        <!-- Match number of table columns exactly -->
                                                                        <td colspan="4">
                                                                            <form class="user-update-form" method="POST" action="/admin_controller">
                                                                                <input type="hidden" name="action" value="update_staff">
                                                                                <input type="hidden" name="userid" id="userid" value="<?= $user['UserID'] ?>">

                                                                                <!-- Row 1: Username, Role, Display Name -->
                                                                                <div class="form-row">
                                                                                    <div class="form-group col-md-4">
                                                                                        <label>Username</label>
                                                                                        <input type="text" name="username" class="form-control" id="username" , value="<?= htmlspecialchars($user['Username']) ?>" required>
                                                                                    </div>
                                                                                    <div class="form-group col-md-4">
                                                                                        <label>Role</label>
                                                                                        <select name="role" class="form-control" id="role" required>
                                                                                            <option value="STAFF" <?= $user['Role'] == 'STAFF' ? 'selected' : '' ?>>Staff</option>
                                                                                            <option value="ADMIN" <?= $user['Role'] == 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="form-group col-md-4">
                                                                                        <label>Display Name</label>
                                                                                        <input type="text" name="display_name" class="form-control" id="display_name" value="<?= htmlspecialchars($user['DisplayName']) ?>">
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Row 2: DOB, Contact, Email -->
                                                                                <div class="form-row">
                                                                                    <div class="form-group col-md-4">
                                                                                        <label>DOB</label>
                                                                                        <input type="date" name="dob" class="form-control" id="dob" value="<?= htmlspecialchars($user['DOB']) ?>">
                                                                                    </div>
                                                                                    <div class="form-group col-md-4">
                                                                                        <label>Contact</label>
                                                                                        <input type="text" name="contact" class="form-control" id="contact" value="<?= htmlspecialchars($user['Contact']) ?>">
                                                                                    </div>
                                                                                    <div class="form-group col-md-4">
                                                                                        <label>Email</label>
                                                                                        <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($user['Email']) ?>">
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Row 4: Password Change -->
                                                                                <div class="form-row">
                                                                                    <div class="form-group col-md-6">
                                                                                        <label>Enter new password. Leave blank if unchanged.</label>
                                                                                        <input type="text" name="password" class="form-control" id="password">
                                                                                    </div>
                                                                                    <div class="form-group col-md-6">
                                                                                        <label>Confirm New Password</label>
                                                                                        <input type="text" name="confirm_password" class="form-control" id="confirm_password">
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Row 4: Update button -->
                                                                                <div class="form-row">
                                                                                    <div class="col text-center mt-3">
                                                                                        <button type="submit" class="btn btn-success px-5">Update</button>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    <!-- Pagination -->
                                                    <?php if ($total_pages > 1): ?>
                                                        <nav>
                                                            <form method="POST" class="d-flex justify-content-center mt-3">
                                                                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                                                    <button type="submit" name="page" value="<?= $p ?>" class="btn btn-sm mx-1 <?= $p == $current_page ? 'btn-primary' : 'btn-light' ?>">
                                                                        <?= $p ?>
                                                                    </button>
                                                                <?php endfor; ?>
                                                            </form>
                                                        </nav>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center mt-3">No users found matching</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Create Staff Form -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <?php
            include('../includes/admin_footer.php');
            ?>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- customizer Panel -->
    <!-- ============================================================== -->
    <?php
    include('../includes/admin_customizer_button.php');
    ?>
    <div class="chat-windows"></div>
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="../../assets/libs/jquery/dist/jquery.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../../assets/libs/popper.js/dist/umd/popper.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!-- apps -->
    <script src="../../dist/js/app.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../dist/js/app.init.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../dist/js/app-style-switcher.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../assets/extra-libs/sparkline/sparkline.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!--Menu sidebar -->
    <script src="../../dist/js/sidebarmenu.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!--Custom JavaScript -->
    <script src="../../dist/js/custom.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
        $(document).ready(function() {
            // Toggle expanded form when username is clicked
            $(document).on("click", ".username-clickable", function() {
                let detailsRow = $(this).closest("tr").next(".user-details-row");
                $(".user-details-row").not(detailsRow).slideUp(); // close other rows
                detailsRow.slideToggle();
            });
        });
    </script>
</body>
<?php
if (isset($_SESSION["search_results"])):
    unset($_SESSION["search_results"]); //Clear after use
endif; ?>

<?php
if (isset($_SESSION["update_staff_status"])): ?>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
        alert("<?= addslashes($_SESSION["update_staff_status"]) ?>");
    </script>
    <?php unset($_SESSION["update_staff_status"]); // clear after use 
    ?>
<?php endif; ?>

<?php
if (isset($_SESSION["delete_staff_status"])): ?>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
        alert("<?= addslashes($_SESSION["delete_staff_status"]) ?>");
    </script>
    <?php unset($_SESSION["delete_staff_status"]); // clear after use 
    ?>
<?php endif; ?>

</html>