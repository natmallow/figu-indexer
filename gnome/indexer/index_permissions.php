<?php


$SECURITY->isLoggedIn();

use gnome\classes\model\Indices;
use gnome\classes\model\User;

$Indices = new Indices();
$User = new User();

$indexId = '';

// individual
if (!empty($_GET['id'])) {
    $indexId = $_GET['id'];
    $SECURITY->indexPermission($indexId);

    // only the admin 
    $currentUsers = $Indices->getIndexUsers($indexId);
    $availableUsers = $Indices->getAvailableUsers($indexId); 
}


?>
<!DOCTYPE html>
<html>

<head>
    <?php include __DIR__ . '../../includes/header.inc.php'; ?>
</head>

<body class="">
    <div id="wrapper">
        <div id="main">
            <div class="inner">
                <?php include '../../includes/title.inc.php'; ?>
                <section>
                    <header class="main">
                        <h2>You are editing Indices Permissions</h2>
                    </header>
                    <div class="row gtr-200">
                        <div class="col-6">
                            <label for="">Available Users</label>
                            <select multiple id="select1" style="height:210px;">
                                <?php foreach ($availableUsers as $row) : ?>

                                <option value="<?= $row["user_id"] ?>">
                                    <?= $row["name_first"] ?>
                                    <?= $row["name_last"] ?>
                                </option>

                                <?php endforeach; ?>
                            </select>

                            <a href="#" class="button small" id="add">&gt;&gt;</a>
                        </div>
                        <div class="col-6">
                            <label for="">Users who have access</label>
                            <select multiple id="select2" style="height:210px;">
                                <?php foreach ($currentUsers as $row) : ?>
                                <option value="<?= $row["user_id"]?>">
                                    <?= $row["name_first"] ?>
                                    <?= $row["name_last"] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <a href="#" class="button small" id="remove">&lt;&lt;</a>
                        </div>
                    </div>

                </section>





            </div>

        </div>
        <?php include '../includes/sidebar.inc.php'; ?>
    </div>

    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <script>
    const ajaxFunction = function(data) {
        $.ajax({
            type: "POST",
            url: "indices_ajax.php", // "/gnome/upload_html.php",
            data: data,
            dataType: 'json',
            cache: false,
            success: function(html) {
                console.log('change good')
            },
            error: function(res) {
                alert('Permission failed', res)
            }
        });
    }

    $('#add').click(function() {

        const selectedAdd = $('#select1 option:selected');

        for (let i = 0; i < selectedAdd.length; i++) {
            // console.log('----------', selectedAdd[i].value)
            const data = {
                "action": 'save-permission',
                "indices_id": <?= $indexId ?>,
                "user_id": selectedAdd[i].value
            }
            ajaxFunction(data);
        }

        return !selectedAdd.remove().appendTo('#select2');
    });

    $('#remove').click(function() {
        const selectedRemove = $('#select2 option:selected');
        for (let i = 0; i < selectedRemove.length; i++) {
            // console.log('----------', selectedRemove[i].value)
            const data = {
                "action": 'remove-permission',
                "indices_id": <?= $indexId ?>,
                "user_id": selectedRemove[i].value
            }
            ajaxFunction(data);
        }
        return !selectedRemove.remove().appendTo('#select1');

    });
    </script>
</body>

</html>