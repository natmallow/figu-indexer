<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use gnome\classes\MessageResource;
use gnome\classes\model\User;

$error = ''; // Initially no error
$index = '';


function postRequest()
{
    global $error;

    $User = new User();

    // If trying to reset password without providing a new one OR using the same old password
    if ((isset($_SESSION['resetPassword']) && !isset($_POST['newpassword'])) ||
        (isset($_SESSION['password']) && $_SESSION['password'] == $_POST['newpassword'])
    ) {
        $error = 'oldPassword';
        return;
    }

    // Perform reset password
    if (isset($_SESSION['resetPassword'])) {
        $User->updateUserPassword($_SESSION['username'], $_POST['newpassword']);

        unset($_SESSION['attempts'], $_SESSION['resetPassword'], $_SESSION['password']);

        $_SESSION['actionResponse_request'] = 'Password Updated';
        header("location: az_login.php");
        exit();
    }

    $user = $User->userLogin($_POST['username'], $_POST['useremail'], $_POST['userpassword']);

    if ($user && $user->username) {
        if ($user->is_locked == 1) {
            $error = 'locked';
            $_SESSION['username'] = $_POST['username'];
        } elseif ($user->is_activated != 1) {
            $error = 'activate';
        } elseif (!$user->is_locked) {
            $_SESSION['username'] = $_POST['username'];
            if ($user->is_reset_password == '1') {
                $_SESSION['resetPassword'] = true;
                $_SESSION['password'] = $user->password;
                header("location: ./az_login.php");
                exit();
            }

            $_SESSION['loggedIn'] = true;
            $_SESSION['filebrowser'] = true;
            $_SESSION['roles'] = $user->roles;

            unset($_SESSION['attempts']);
            header("location: index.php");
        }
    } else {
        if ($_SESSION['username'] == $_POST['username']) {
            $_SESSION['attempts'] = isset($_SESSION['attempts']) ? $_SESSION['attempts'] + 1 : 1;
        } else {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['attempts'] = 1;
        }

        // Lock user after 7 attempts
        if ($_SESSION['attempts'] >= 7) {
            $User->lockUser($_SESSION['username']);
            $error = 'locked';
        } else {
            $error = 'generalErr';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    postRequest();
}
?>

<html>

<head>
    <?php include __DIR__ . '/includes/head.inc.php'; ?>

</head>

<body class="">
    <div class="container">
        <header id="header" class="col-12 text-center">
            <div class="mb-3">
                <h1>
                    <a href=<?= $index ?>>
                        <strong>FIGU</strong>-Interessengruppe f&uuml;r Missionswissen Northern Arizona</a> - Login
                </h1>
                <a href="/" class="text-decoration-none"><strong>To Main Page</strong></a>
            </div>
            <div class="d-none d-md-flex image-header">
                <img src="/media/images/geisteslehre.jpg">
                <img src="/media/images/meditation_symbol.png">
                <img src="/media/images/sssc.jpg">
                <img src="/media/images/crusade-against-overpopulation.jpg">
                <img src="/media/images/peace-symbol.jpg">
            </div>
        </header>
        <section>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <img src="../media/images/painting.jpg" alt="" class="img-fluid">
                </div>


                <div class="col-md-6">

                    <div class="col-md-12">
                        <div class="list-group" id="list-tab" role="tablist">
                            <a class="list-group-item list-group-item-action active" id="figuaz-login" data-toggle="list" href="#list-figuaz-login" role="tab" aria-controls="figuaz-login">FiguAz Admin</a>
                            <a class="list-group-item list-group-item-action" id="indexer-login" data-toggle="list" href="#list-indexer-login" role="tab" aria-controls="indexer-login">Figu Indexer Admin</a>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="list-figuaz-login" role="tabpanel" aria-labelledby="figuaz-login">
                                Jo
                                <?php if (!isset($_SESSION['resetPassword'])) : ?>
                                    <form method="post">
                                        <?php if (in_array($error, ['generalErr', 'locked', 'activate'])) : ?>
                                            <div class="alert alert-danger">
                                                <i class="bi bi-exclamation-octagon me-1"></i>
                                                <?php if ($error == 'generalErr') : ?>
                                                    Error, please check your input and try again.
                                                <?php elseif ($error == 'locked') : ?>
                                                    The account <strong><?= filter_input(INPUT_POST, 'username') ?></strong> is locked. Please contact us at info@figuarizona.org for information.
                                                <?php elseif ($error == 'activate') : ?>
                                                    The account <strong><?= filter_input(INPUT_POST, 'username') ?></strong> has not been reviewed. If you have been waiting for a long time, please contact us at info@figuarizona.org to unlock.
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="email" name="useremail" id="useremail" class="form-control" placeholder="Email" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" name="userpassword" class="form-control" placeholder="Password" required>
                                        </div>
                                        <div class="mb-2 d-flex gap-3">
                                            <a href="forgot" class="btn btn-secondary w-100"><i class="ri-file-unknow-fill"></i> Forgot Password</a>
                                            <a href="access" class="btn btn-secondary w-100"><i class="bx bx-happy-beaming"></i> Request Access</a>
                                            <button type="submit" name="submitted" value="submitted" class="btn btn-primary w-100"><i class="bi bi-door-open-fill"></i> Beam In!</button>
                                        </div>
                                    </form>
                                <?php else : ?>
                                    <!-- Reset password form -->
                                    <form method="post">
                                        <?php if ($error == 'oldPassword') : ?>
                                            <div class="alert alert-danger">
                                                <p>Error, please check your input and try again.</p>
                                                <p>Your password cannot be the same as your old one.</p>
                                            </div>
                                        <?php endif; ?>
                                        <label>Please Enter a new password</label>
                                        <div class="mb-3">
                                            <input type="password" name="newpassword" id="password" class="form-control" placeholder="New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" id="confirm_password" class="form-control" placeholder="Retype Password">
                                        </div>
                                        <div>
                                            <button type="submit" name="submitted" value="submitted" class="btn btn-primary"><i class="bx bxs-user-check"></i> Save Password</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>

                            <div class="tab-pane fade" id="list-indexer-login" role="tabpanel" aria-labelledby="indexer-login">
                                <?php if (!isset($_SESSION['resetPassword'])) : ?>
                                    <form method="post">
                                        <?php if (in_array($error, ['generalErr', 'locked', 'activate'])) : ?>
                                            <div class="alert alert-danger">
                                                <i class="bi bi-exclamation-octagon me-1"></i>
                                                <?php if ($error == 'generalErr') : ?>
                                                    Error, please check your input and try again.
                                                <?php elseif ($error == 'locked') : ?>
                                                    The account <strong><?= filter_input(INPUT_POST, 'username') ?></strong> is locked. Please contact us at info@figuarizona.org for information.
                                                <?php elseif ($error == 'activate') : ?>
                                                    The account <strong><?= filter_input(INPUT_POST, 'username') ?></strong> has not been reviewed. If you have been waiting for a long time, please contact us at info@figuarizona.org to unlock.
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="email" name="useremail" id="useremail" class="form-control" placeholder="Email" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" name="userpassword" class="form-control" placeholder="Password" required>
                                        </div>
                                        <div class="mb-2 d-flex gap-3">
                                            <a href="forgot" class="btn btn-secondary w-100"><i class="ri-file-unknow-fill"></i> Forgot Password</a>
                                            <a href="access" class="btn btn-secondary w-100"><i class="bx bx-happy-beaming"></i> Request Access</a>
                                            <button type="submit" name="submitted" value="submitted" class="btn btn-primary w-100"><i class="bi bi-door-open-fill"></i> Beam In!</button>
                                        </div>
                                    </form>
                                <?php else : ?>
                                    <!-- Reset password form -->
                                    <form method="post">
                                        <?php if ($error == 'oldPassword') : ?>
                                            <div class="alert alert-danger">
                                                <p>Error, please check your input and try again.</p>
                                                <p>Your password cannot be the same as your old one.</p>
                                            </div>
                                        <?php endif; ?>
                                        <label>Please Enter a new password</label>
                                        <div class="mb-3">
                                            <input type="password" name="newpassword" id="password" class="form-control" placeholder="New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" id="confirm_password" class="form-control" placeholder="Retype Password">
                                        </div>
                                        <div>
                                            <button type="submit" name="submitted" value="submitted" class="btn btn-primary"><i class="bx bxs-user-check"></i> Save Password</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>


                    </div>
                </div>


            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let password, confirm_password;

            try {
                password = document.getElementById("password");
                confirm_password = document.getElementById("confirm_password");

                password.addEventListener('change', validatePassword);
                confirm_password.addEventListener('keyup', validatePassword);
            } catch (err) {
                console.error('Error occurred:', err);
            }

            function validatePassword() {
                if (password.value !== confirm_password.value) {
                    confirm_password.setCustomValidity("Passwords Don't Match");
                } else {
                    confirm_password.setCustomValidity('');
                }
            }
        });
    </script>
</body>

</html>