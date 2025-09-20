<script>
    (function($) {

        imageSwitch = function(targetId, imageName) {
            const ele = $('#preview-container');
            ele.empty();
            const img = document.createElement('img');
            img.src = `${imageName}`;
            ele.append(img);
        }

        imageDeleteHandler = function(targetUpdate) {

            $('.move-to-trash').on('click', function(event) {
                if (window.confirm('Are you sure you want to delete image?')) {

                    var file = $(event.target).data('delete');
                    $.ajax({
                        url: "/gnome/uploader.php?file=" + file,
                        type: 'DELETE',
                        dataType: 'json',
                        success: function(resp) {
                            console.log(resp, 'hello');
                            $(event.target.parentNode.parentNode).css("display", "none");
                        }
                    });

                }
            })
        }


        imageSelectHandler = function(targetUpdate) {
            $('.fileImg').on('click', function(event) {
                var file = $(event.target).data('file');
                var path = $(event.target).data('dir');
                imageSwitch('preview-container', file);
                $('#' + targetUpdate).val(path);
            })
        }

        docSelectHandler = function(targetUpdate) {
            $('.fileDoc').on('click', function(event) {
                var path = $(event.target).data('dir');
                $('#' + targetUpdate).val(path);
            })
        }


        goBackHandler = function(targetUpdate, handlers) {
            $('.goBack').on('click', function(event) {

                var path = $(event.target).data('dir');
                fileFolder = path.replace("\\", "\\\\");
                $.ajax({
                    url: "/../gnome/indexer/upload_html.php?folder=" + path + "&back=true",
                    cache: false,
                    success: function(html) {
                        modalData.empty().append(html).ready(function() {
                            $.each(handlers, function(index, item) {
                                window[item](targetUpdate, handlers);
                            })
                        });
                        modal.css("display", "block");
                    }
                });
            })
        }

        addFolderHandler = function(targetUpdate, handlers) {
            $('.addDirectory').on('click', function(event) {

                var path = $(event.target).data('dir');
                // fileFolder = path.replace("\\", "\\\\");
                var folder = prompt("Please enter name of folder:", "");

                var re = /^(\(?\+?[a-z]*\)?)?[a-z_\- \(\)]*$/;

                if (folder == null || folder.trim() == "" || !re.exec(folder)) {

                    alert("Folder name invalid! Use lowercase char only.", re.exec(folder));
                } else {
                    $.ajax({
                        url: "/gnome/uploader.php?action=newDir",
                        type: 'POST',
                        data: {
                            dir: path,
                            name: folder
                        },
                        dataType: 'json',
                        success: function(resp) {
                            alert('Folder Created');
                            $('#openDirectoryView').trigger('click');
                            //console.log(resp, 'folder');
                        }
                    });
                }
            })
        }

        openFolderHandler = function(targetUpdate, handlers) {
            $('.folderDir').on('click', function(event) {
                var path = $(event.target).data('dir');
                fileFolder = path.replace("\\", "\\\\");
                $.ajax({
                    url: "/../gnome/indexer/upload_html.php?folder=" + fileFolder,
                    cache: false,
                    success: function(html) {
                        modalData.empty().append(html).ready(function() {
                            $.each(handlers, function(index, item) {
                                window[item](targetUpdate, handlers);
                            })
                        });
                        modal.css("display", "block");
                    }
                })
            })
        }

        initImageModal = function(modalNameId, openBtnId, closeBtnId, modalDataContainerId, targetUpdate, handlers = []) {

            var Modal = new bootstrap.Modal(
                document.getElementById(modalNameId), {
                    keyboard: true
                });


            // add callbacks
            handlers.push('goBackHandler');
            handlers.push('openFolderHandler');
            handlers.push('addFolderHandler');

            // try {  
            modal = $("#" + modalNameId);
            // Get the button that opens the modal
            modalData = $("#" + modalDataContainerId);
            // Get the button that opens the modal               
            openBtn = $("#" + openBtnId);
            // Get the <span> element that closes the modal
            closeBtn = $("#" + closeBtnId);
            // When the user clicks on the button, open the modal

            openBtn.on('click', function() {
                //  $("#container-image").load("/gnome/indexer/upload_html.php")
                // console.log(modal);
                $.ajax({
                    url: "/../gnome/indexer/upload_html.php",
                    cache: false,
                    success: function(html) {
                        modalData.empty().append(html).ready(function() {

                            $.each(handlers, function(index, item) {
                                window[item](targetUpdate, handlers);
                            })

                        });
                        // modal.css("display", "block");
                        Modal.show();
                    }
                });
            });
            // When the user clicks on <span> (x), close the modal
            closeBtn.on('click', function() {
                // modal.css("display", "none");
                Modal.hide();
            });
            // When the user clicks anywhere outside of the modal, close it
            $(window).click(function(event) {
                var target = $(event.target);
                if (target.is(modal)) {
                    // modal.css("display", "none");
                    Modal.hide();
                }
            });

        }


    })(jQuery);
</script>