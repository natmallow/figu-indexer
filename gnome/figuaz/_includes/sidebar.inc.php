<?php
    use gnome\classes\model\Publication;
    $Publication = new Publication();

    $pages = [
       ["name"=>"users", "url"=> "/gnome/figuaz/users.php", "role" => ["admin", "user"], "icon"=> "ri-file-user-line"],
       ["name"=>"uploads", "url"=> "/gnome/figuaz/uploads.php", "role" => ["admin", "user"], "icon"=> "ri-folder-upload-line"],
       ["name"=>"indices", "url"=> "/gnome/indexer/indices.php", "role"  => ["indexer_admin", "indexer_user"], "icon"=> "bx bxs-dice-1"],
       ["name"=>"publication uploader", "url"=> "/gnome/indexer/publications_index.php", "role" => ["indexer_admin", "indexer_user"], "icon"=> "bx bxs-dice-2"],
       ["name"=>"keywords index", "url"=> "/gnome/indexer/keywords_index.php", "role" => ["indexer_admin", "indexer_user"], "icon"=> "bx bxs-dice-3"]
    ];
?>


    <!-- <header class="row">
        <div class="col-6"><h2>Admin Menu</h2></div> 
        <div class="col-6" style="text-align:right"><button  class="button small" onclick="logout()">Logout</button></div> 
    </header> -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <?php    
                foreach ($pages as $key => $value) {
                    $collape = "collapsed";
                    if ($SECURITY->roles($value["role"])) {
                        
                        if (strpos($_SERVER['REQUEST_URI'],$value["url"]) !== false) {
                            $collape ="";
                            }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?=$collape?>" href="<?=$value["url"]?>?lang=<?= lang() ?>">
                        <i class="<?=$value["icon"]?>"></i>
                        <span><?=$value["name"]?></span>
                        </a>
                    </li>
                    <?php
                    }
                }
            ?>
        </ul>
    </aside>

