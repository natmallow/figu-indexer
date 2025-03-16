<?php
@session_start();

use gnome\classes\model\User;
use gnome\classes\Director;

$User = new User();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user = (object) $User->getUserByEmail($_POST['useremail']);
    if (isset($user->username)) {
        $s = new Director();
        $s->resetPasswordEmail($user->useremail, $user->name_first);
    }

    $_SESSION['passwordSent'] = true;
    header("location:" . $_SERVER['HTTP_REFERER']);
    exit();
}
?>

<html>

<head>
    <?php include __DIR__ . '/includes/head.inc.php'; ?>
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">

            <header id="header" class="col-12 text-center">
                <h1>Forgot password to FIGU Indexer</h1>
                <a href="login" class="d-block mb-3"><strong>Back to login</strong></a>
            </header>

            <div class="col-lg-6">
                <img src="../media/images/painting.jpg" alt="" class="img-fluid mb-4">
            </div>

            <div class="col-lg-6">

                <?php if (!isset($_SESSION['passwordSent'])) : ?>
                    <p>If you've forgotten your password, please enter your registered email address in the provided field. Once submitted, you'll receive a link to your inbox. Clicking on this link will guide you to a secure page where you can set and save a new password. Make sure to use this link promptly, as it may expire for security reasons.</p>
                    <form method="post">
                        <div class="mb-3">
                            <input type="email" name="useremail" required class="form-control" id="useremail" placeholder="Email">
                        </div>

                        <button type="submit" name="submitted" value="submitted" class="btn btn-primary w-100 mb-2">Send Reset link</button>
                    </form>
                <?php else : ?>
                    <div class="alert alert-info">
                    If you are registered with us, a password reset link will be sent to your email. If you encounter any issues with the link, please reach out to us at info@figuarizona.org for assistance.
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script>
        var password = document.getElementById("password");
        var confirm_password = document.getElementById("confirm_password");

        function validatePassword() {
            if (password && confirm_password && password.value !== confirm_password.value) {
                confirm_password.setCustomValidity("Passwords Don't Match");
            } else if (confirm_password) {
                confirm_password.setCustomValidity('');
            }
        }

        if (password) {
            password.onchange = validatePassword;
        }
        if (confirm_password) {
            confirm_password.onkeyup = validatePassword;
        }
    </script>


</body>

</html>