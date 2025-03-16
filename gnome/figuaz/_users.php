<?php

$SECURITY->isLoggedIn();

use gnome\classes\MessageResource as MessageResource;

$User = new gnome\classes\model\User();
$Role = new gnome\classes\model\Role();

$username = '';
$email = '';
$password = '';
$name_first = '';
$name_last = '';
$phone = '';
$is_locked = '';
$is_reset_password = '';
$is_activated = '';
$reason = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST['action'] == 'edit') {
        $User->updateUser();
        $User->updateUserRoles();
    } elseif ($_POST['action'] == 'add') {
        $User->addUser();
    }

    header("Location: ./users.php");
    exit();
}

if (!empty($_GET['id'])) {
    $user = $User->getUserByUsername($_GET['id']);
    extract($user);
} else {
    $user = $User->getUsers();
}
$availableRoles = $Role->getRoles();
?>
<!doctype html>
<html>

<head>
    <?php include __DIR__ . '/includes/head.inc.php'; ?>
    <script type="text/javascript">
        $().ready(function() {
            $('#add').click(function() {
                return !$('#select1 option:selected').remove().appendTo('#select2');
            });
            $('#remove').click(function() {
                return !$('#select2 option:selected').remove().appendTo('#select1');
            });
        });
    </script>
</head>

<body class="">
    <?php include __DIR__ . '/includes/topnav.inc.php'; ?>
    <?php include 'includes/sidebar.inc.php'; ?>
    <main id="main" class="main">


        <?php include 'includes/title.inc.php'; ?>

        <section class="section">
            <header class="py-3 mb-4 border-bottom">
                <div class="d-flex flex-wrap justify-content-center">
                    <div class="col-12">
                        <span class="fs-4">You are editing users</span>
                    </div>
                </div>
            </header>

            <?php include 'includes/head-resp.inc.php'; ?>

            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <span class="fs-2 fw-bold">Available Users</span>
                    <a href="/gnome/users.php?action=add" class="btn btn-primary btn-sm">Create New</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php include 'includes/head-resp.inc.php'; ?>
                </div>
            </div>
            <?php if (!empty($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'add')) : ?>


                <form id="sectionForm" method="POST" class="row g-3" action="users.php" onsubmit="return runSubmit()">


                    <div class="col-12">
                        <input type="hidden" name="action" value="<?= $_GET['action'] ?>">
                        <label for="username" class="form-label">User Name:</label>
                        <?php if ($_GET['action'] == 'edit') : ?>
                            <input type="hidden" name="username" value="<?= $username ?>">
                            <input type="input" value="<?= $username ?>" class="form-control disabled-input" disabled="disabled">
                        <?php else : ?>
                            <input type="text" name="username" id="username" class="form-control" value="" maxlength="25" required>
                        <?php endif ?>
                    </div>




                    <div class="col-sm-6">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" value="<?= $password ?>" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                    </div>
                    <div class="col-sm-6">
                        <label for="confirm_password" class="form-label">Repeat Password:</label>
                        <input type="password" id="confirm_password" class="form-control" value="<?= $password ?>">
                    </div>



                    <div class="col-6">
                        <label for="select1">Available Roles</label>
                        <div class="input-group mb-3">

                            <select class="form-select form-select-sm text-end" size="7" multiple aria-label="multiple select example" id="select1">
                                <?php
                                $activeRoles = [];
                                foreach ($availableRoles as $row) :
                                    if (in_array($row["role_name"], json_decode($roles))) :
                                        $activeRoles[] = $row;
                                    else :
                                ?>
                                        <option value="<?= $row["role_id"] ?>">
                                            <?= $row["role_name"] ?>
                                        </option>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </select>
                            <a href="#" class="btn btn-success d-flex align-items-center" id="add">&gt;&gt;</a>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="select2">Users Roles</label><br>
                        <div class="input-group mb-3">
                            <a href="#" class="btn btn-danger d-flex align-items-center" id="remove">&lt;&lt;</a>
                            <select multiple id="select2" class="form-select form-select-sm" size="7" name="roles[]">
                                <?php foreach ($activeRoles as $row) : ?>
                                    <option value="<?= $row["role_id"] ?>">
                                        <?= $row["role_name"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>



                    <div class="col-sm-6">
                        <label for="name_first" class="form-label">First Name:</label>
                        <input type="text" name="name_first" class="form-control" id="name_first" value="<?= $name_first ?>" required minlength="5" maxlength="49">
                    </div>

                    <div class="col-sm-6">
                        <label for="name_last" class="form-label">Last Name:</label>
                        <input type="text" name="name_last" class="form-control" id="name_last" value="<?= $name_last ?>" required minlength="5" maxlength="49">
                    </div>

                    <div class="col-sm-6">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" class="form-control" id="email" value="<?= $email ?>" required minlength="5" maxlength="120">
                    </div>

                    <div class="col-sm-6">
                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" class="form-control" id="phone" value="<?= $phone ?>" required minlength="5" maxlength="15">
                    </div>

                    <div class="col-12">
                        <label for="reason">reason:</label>
                        <input type="text" name="reason" class="form-control" id="reason" value="<?= $reason ?>" disabled>
                    </div>


                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_activated" class="form-check-input" id="is_activated" value="1" <?php if ($is_activated == '1') echo "checked='checked'"; ?>>
                            <label class="form-check-label" for="is_activated">Activate user</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="is_locked" class="form-check-input" id="is_locked" value="1" <?php if ($is_locked == '1') echo "checked='checked'"; ?>>
                            <label class="form-check-label" for="is_locked">Lock user</label>
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="is_reset_password" class="form-check-input" id="is_reset_password" value="1" <?php if ($is_reset_password == '1') echo "checked='checked'"; ?>>
                            <label class="form-check-label" for="is_reset_password">Check to reset password on next login</label>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-between">
                        <a href="/gnome/users.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                </form>


            <?php else : ?>
                <div class="col-md-12">

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th colspan="4">Actions</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($user as $row) : ?>
                                <tr id='row-<?= $row["username"] ?>'>
                                    <td><?= $row["name_first"] ?>, <?= $row["name_last"] ?>
                                        <?php if ($row["is_activated"] == '0') : ?>
                                            <input onclick="runActivate('<?= $row['username'] ?>')" id="is_activated-<?= $row["username"] ?>" type="checkbox" <?php if ($row["is_activated"] == '1') echo "checked='checked'"; ?>>
                                            <label for="is_activated-<?= $row["username"] ?>" style="color:#f48800; font-size: .8em;padding-right:0px;">Activate</label>
                                        <?php else : ?>
                                            <span style="color: rgb(9 125 93); font-size: .8em; padding-right:0px; font-weight:bold;">Activated</span>
                                        <?php endif; ?>
                                    <td><?= $row["username"] ?>
                                    <td><?= $row["email"] ?>
                                    <td><?= $row["phone"] ?>
                                    <td style="border-left: 1px #ccc solid">
                                        <a href="/gnome/users.php?id=<?= $row["username"] ?>&action=edit" class="admin-link">Edit</a>
                                    <td>
                                        <a href="/gnome/users_permissions.php?id=<?= $row["username"] ?>&action=edit" class="admin-link">Permissions</a>

                                    <td> <input onclick="runLocked('<?= $row['username'] ?>')" id="is_locked-<?= $row["username"] ?>" type="checkbox" <?php if ($row["is_locked"] == '1') echo "checked='checked'"; ?>>
                                        <label for="is_locked-<?= $row["username"] ?>" style="color:#f48800; font-size: .8em;padding-right:0px;">Locked</label>
                                    <td> <a onclick="runPswdReset('<?= $row['username'] ?>')" id="is_reset_password-<?= $row["username"] ?>" class="admin-link">
                                            Password Reset</a>

                                    <td style="background-color: #fff">
                                        <a title="Delete <?= $row['username'] ?>" onclick="return confirm('A you sure you want to Remove \n\n<?= $row['name_first'] ?>, <?= $row['name_last'] ?> \n\n from users?')?runDelete('<?= $row['username'] ?>'):false;" class="admin-link">Remove</a>

                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>

                </div>
            <?php endif; ?>
            </div>
        </section>
    </main>


    <?php include __DIR__ . '/../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/includes/footer.inc.php'; ?>
    <script>
        function runActivate(username) {
            var d = document.getElementById('notification');
            d.style.display = 'none';
            var fetchPromise = fetch(`/gnome/user.php?username=${username}&action=activate`);
            fetchPromise.then(response => {
                return response.json();
            }).then(data => {
                // console.log(data);
                if (data.newValue === '1') {
                    d.innerHTML = `User ${username} has been activated`;
                }

                d.className = 'notification fadeOut';
                d.style.display = 'block';
            });
        };

        function runLocked(username) {
            var d = document.getElementById('notification');
            d.style.display = 'none';
            var fetchPromise = fetch(`/gnome/user.php?username=${username}&action=toggleLock`);
            fetchPromise.then(response => {
                return response.json();
            }).then(data => {
                // console.log(data);
                if (data.newValue === '1') {
                    d.innerHTML = `User ${username} has been locked`;
                } else {
                    d.innerHTML = `User ${username} is unLocked`;
                }
                d.className = 'notification fadeOut';
                d.style.display = 'block';
            });
        };

        function runPswdReset(username) {
            var d = document.getElementById('notification');
            d.style.display = 'none';
            var fetchPromise = fetch(`/gnome/user.php?username=${username}&action=resetPswd`);
            fetchPromise.then(response => {
                return response.json();
            }).then(() => {
                // console.log(data);
                d.innerHTML = 'Password has been reset';
                d.className = 'notification fadeOut';
                d.style.display = 'block';
            });
        };

        function runDelete(username) {
            var d = document.getElementById('notification');
            var dr = document.getElementById(`row-${username}`);
            d.style.display = 'none';
            var fetchPromise = fetch(`/gnome/user.php?username=${username}&action=deleteUser`);
            fetchPromise.then(response => {
                return response.json();
            }).then(data => {
                // console.log(data);
                d.innerHTML = data.msg;
                d.className = 'notification fadeOut';
                d.style.display = 'block';
                dr.innerHTML = ''
                dr.style.display = 'none';

            });
        };

        try {
            var password = document.getElementById("password"),
                confirm_password = document.getElementById("confirm_password");
        } catch (err) {

        }

        function validatePassword() {
            if (password.value != confirm_password.value) {
                confirm_password.setCustomValidity(`Passwords Don't Match`);
            } else {
                confirm_password.setCustomValidity('');
            }
        }
        try {
            password.onchange = validatePassword;
            confirm_password.onkeyup = validatePassword;
        } catch (err) {

        }

        function runSubmit() {
            const mSelect = document.querySelectorAll('#select2 option');
            for (i = 0; i < mSelect.length; i++) {
                mSelect[i].selected = true;
            }
            return true;
        }
    </script>
</body>

</html>