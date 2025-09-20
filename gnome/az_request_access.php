<?php

@session_start();

use gnome\classes\MessageResource;
use gnome\classes\Director;
use gnome\classes\model\User;

$User = new User;


$username = isset($_POST['username']) ? $_POST['username'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$password = '';
$name_first = isset($_POST['name_first']) ? $_POST['name_first'] : '';
$name_last = isset($_POST['name_last']) ? $_POST['name_last'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
$success = false;

$successTxt = "Your request has been submitted";

if (!isset($_SESSION['actionResponse_request'])) {
    $_SESSION['actionResponse_request'] = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $doesUserExits = $User->checkUser($_POST['username'], $_POST['email']);

    if ($doesUserExits == '1') {
        $_SESSION['actionResponse_request'] = "Choose a different Username and/or Email";
    } elseif ($_POST['robot'] !== 'true') {
        $_SESSION['actionResponse_request'] = "Your request has not been submitted. <br>Click '>> human check' <br>then select the 'I am not a bot'. checkbox";
    } else {
        $User->addUser();
        $_SESSION['success_request'] = $successTxt;
        $s = new Director();
        $s->emailRequestAccess($email, $name_first);
        header('location:' . $_SERVER['PHP_SELF']);
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Request Access</title>
    <?php include __DIR__ . '/includes/head.inc.php'; ?>
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">

            <header id="header" class="col-12 text-center">
                <h1>Request Access to FIGU Indexer</h1>
                <a href="login" class="d-block mb-3"><strong>Back to login</strong></a>
            </header>
            <section class="row">
                <?php include 'includes/head-resp.inc.php'; ?>
                <div class="col-lg-6">
                    <img src="../media/images/painting.jpg" alt="" class="img-fluid mb-4">
                </div>
                <?php
                if (!($successTxt == @$_SESSION['success_request'])) {
                ?>
                    <div class="col-lg-6">
                        <form id="sectionForm" method="POST" action="az_request_access.php">
                            <input type="hidden" name="robot" id="robot" value="">

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="name_first" class="form-label">First Name:</label>
                                        <input type="text" name="name_first" id="name_first" class="form-control" required minlength="3" maxlength="49" value="<?= $name_first ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="name_last" class="form-label">Last Name:</label>
                                        <input type="text" name="name_last" id="name_last" class="form-control" required minlength="3" maxlength="49" value="<?= $name_last ?>">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email:</label>
                                        <input type="email" name="email" id="email" class="form-control" required minlength="7" maxlength="120" value="<?= $email ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone:</label>
                                        <input type="text" name="phone" id="phone" class="form-control" required minlength="10" maxlength="15" value="<?= $phone ?>">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">User Name:</label>
                                        <input type="text" name="username" id="username" class="form-control" required maxlength="25" value="<?= $username ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="passwordInpt" class="form-label">Password:</label>
                                        <input type="password" id="passwordInpt" name="password" class="form-control" required pattern="(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])\S{8,20}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or 20 characters">
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Repeat Password:</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Reason for Request:</label>
                                        <select name="reason" class="form-select">
                                            <option value="Access to Indexer">Access to Indexer</option>
                                            <option value="Access to figu members">Access to figu members</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-3 text-end">
                                    <button type="button" id="showBot" class="btn btn-success w-100"><i class="bi bi-person-check"></i> human check</button>
                                </div>

                                <div class="col-lg-4 col-md-3 text-end">
                                    <div id="i_am_not_a_robot" class="d-flex justify-content-center align-items-center" style="height: 40px;"></div>
                                </div>

                                <div class="col-lg-4 col-md-4 text-end">
                                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            initPasswordValidation();
                            initNotRobotCheck();
                        });

                        function initPasswordValidation() {
                            const passwordInput = document.getElementById('passwordInpt');
                            const confirmPasswordInput = document.getElementById('confirm_password');

                            function validatePassword() {
                                confirmPasswordInput.setCustomValidity(
                                    passwordInput.value !== confirmPasswordInput.value ? "Passwords Don't Match" : ''
                                );
                            }

                            passwordInput.addEventListener("change", validatePassword);
                            confirmPasswordInput.addEventListener("keyup", validatePassword);
                        }

                        function initNotRobotCheck() {
                            const showBotButton = document.querySelector('#showBot');
                            const robotContainer = document.getElementById('i_am_not_a_robot');
                            const robotFlag = document.getElementById('robot');

                            showBotButton.addEventListener('click', function(event) {
                                // Hide button
                                event.target.style.display = 'none';

                                const tagString = `
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" onclick="setRobotFlag()" id="is_robot">
                                        <label class="form-check-label" for="is_robot">
                                            I am not a bot!
                                        </label>
                                    </div>
                                `;

                                robotContainer.insertAdjacentHTML('beforeend', tagString);
                            });

                            window.setRobotFlag = function() {
                                robotFlag.value = 'true';
                            }
                        }
                    </script>
                <?php } else { ?>
                    <div class="row">

                        <div class="col-4 ">
                            <span class="image main"><img src="../media/images/painting.jpg" alt=""></span>
                        </div>
                        <div class="col-4  align-center">
                            <h2>Thank You <br>Request has been submitted.</h2>
                            <a href="/">Return to main page</a>
                        </div>
                        <div class="col-4 ">
                            <span class="image main"><img src="../media/images/painting.jpg" alt=""></span>
                        </div>
                    </div>
                <?php } ?>
            </section>
        </div>
    </div>




</body>

</html>