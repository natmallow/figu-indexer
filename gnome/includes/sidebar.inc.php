<?php
    use gnome\classes\model\Publication;
    $Publication = new Publication();

    $pages = [
       ["name"=>"sections", "url"=> "/gnome/figuaz/sections.php", "role"=> ["admin", "user"], "icon"=> "bi bi-intersect"],
       ["name"=>"articles", "url"=> "/gnome/figuaz/articles.php", "role" => ["admin", "user"], "icon"=> "ri-article-fill"],
       ["name"=>"users", "url"=> "/gnome/figuaz/users.php", "role" => ["admin", "user"], "icon"=> "ri-file-user-line"],
       ["name"=>"uploads", "url"=> "/gnome/figuaz/uploads.php", "role" => ["admin", "user"], "icon"=> "ri-folder-upload-line"],
       ["name"=>"templates", "url"=> "/gnome/figuaz/templates.php", "role" => ["admin", "user"], "icon"=> "bi bi-columns"],
       ["name"=>"emails", "url"=> "/gnome/figuaz/emails.php", "role"  => ["admin", "user"], "icon"=> "bi bi-mailbox"],
       ["name"=>"virtuals", "url"=> "/gnome/figuaz/virtuals.php", "role"  => ["admin", "user"], "icon"=> "bx bxs-cube"],
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

