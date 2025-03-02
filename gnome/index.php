<?php

$SECURITY->isLoggedIn();

?>

<html>

<head>
    <?php include __DIR__ . '/includes/head.inc.php'; ?>
</head>

<body class="">
    <?php include __DIR__ . '/includes/topnav.inc.php'; ?>
    <?php include 'includes/sidebar.inc.php'; ?>
    <main id="main" class="main">

        <?php include 'includes/title.inc.php'; ?>

        <section class="section">
            <header class="py-3 mb-4 border-bottom">
                <div class="col-12">
                    <span class="fs-4">
                        Welcome to FIGU AZ Site Admin </span>
                    </span>
                </div>
            </header>

            <div class="row">
                <div class="col-12 col-12-medium">
                    <?php
                        if (isset($_SESSION['actionResponse'])  && $_SESSION['actionResponse'] != '') {
                            echo "<div class='notification'>$_SESSION[actionResponse]</div>";
                        }

                        $_SESSION['actionResponse'] = '';
                    ?>
                </div>
                <div class="col-12 col-12-medium">
                    <span class="image main"><img src="../media/images/login-to-az.jpg" alt=""></span>
                </div>
            </div>
        </section>

    </main>

    <?php include __DIR__ . '/includes/footer.inc.php'; ?>
</body>

</html>