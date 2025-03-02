<?php
$SECURITY->isLoggedIn();

use gnome\classes\DBConnection;
use gnome\classes\model\Publication;

$lang = lang();

$Publication = new Publication();

$pub_id = filter_input(INPUT_GET, 'type') ? filter_input(INPUT_GET, 'type') : '';

$publicationType = (object) $Publication->getPublicationType($pub_id);

?>
<!DOCTYPE html>
<html>
<head>
    <script>
        createPublication = async (e) => {

            e.target.disabled = true;

            const publication_id = document.getElementById('publicationAbbr').value + document.getElementById(
                'publicationAppend').value.trim()
            const publication_type_id = document.getElementById('publicationTypeId').value
            const url = 'publication_ajax.php';
            const errBlk = document.querySelector('#notification-create');

            const form = new FormData();
            form.append('publication_id', publication_id);
            form.append('publication_type_id', publication_type_id);
            form.append('action', 'addPublication');

            const options = {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: form
            };

            const response = await fetch(url, options);

            if (response) {
                e.target.disabled = false;
            }

            if (response.status >= 200 && response.status <= 204) {
                let data = await response.json();
                // publication.php?action=edit&id=RATG33&pub_type=1

                window.location = `publication.php?action=edit&id=${data.publication_id}&pub_type=${data.pub_type}`;
            } else if (response.status == 208) {
                errBlk.innerHTML = `The Publication "${publication_id}" already exists.`
                errBlk.style.display = 'block';
            } else if (response.status == 400) {
                errBlk.innerHTML = `The Publication type "${publication_type_id}" is invalid. Plase start over.`
                errBlk.style.display = 'block';
            } else {
                console.log(`Something went wrong, the server code: ${response.status}`);
            }

        }

        listenerBlock = () => {
            // add listen
            document.getElementById('create-publication').addEventListener('click', (event) => {
                var form = document.getElementById('publication_create');
                if (form.checkValidity()) {
                    createPublication(event);
                } else {
                    form.reportValidity();
                    return;
                }
            })
        }
    </script>
</head>

<body>


    <form id="publication_create">
        <div class="modal-header">
            <h5 class="modal-title">Create the publication reference</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12" style="text-align:center">
                    <span class="notification" id="notification-create" style="display:none;">
                    </span>
                </div>
                <div class="col-12">

                    <input type="hidden" id="publicationTypeId" name="publicationTypeId" value="<?= $publicationType->publication_type_id ?>">
                    <input type="hidden" id="publicationAbbr" name="publicationAbbr" value="<?= $publicationType->abbreviation ?>">

                    <div class="row">
                        <div class="col-6">
                            <label>Publication abbreviation:</label>
                            <input type="text" name="" disabled="disabled" value="<?= $publicationType->abbreviation ?>" class="form-control">
                        </div>
                        <div class="col-6">
                            <label for="publicationAppend">Publication number/name:</label>
                            <input type="text" name="publicationAppend" id="publicationAppend" required="required" pattern="[A-Za-z0-9]{1,40}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="create-publication" class="btn btn-primary">Create</button>
        </div>
    </form>



</body>

</html>