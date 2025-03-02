<?php

use gnome\classes\DBConnection;

$database = new DBConnection();

$lang = empty($_GET['lang']) ? 'en' : htmlspecialchars($_GET["lang"]);

$sqlSelect = "SELECT s.*, sb.name as top_name, sb.description  FROM sections s 
                                    LEFT JOIN sections_body sb ON (s.id_sections = sb.id_sections and sb.language = :lang)
                                    WHERE s.is_active = 1 AND s.is_on_homepage = 1 ORDER BY sb.name ASC";

$pdoc = $database->dbc->prepare($sqlSelect);
$pdoc->execute([':lang' => $lang]);


// Use this method to run select statements
$sections = $pdoc->fetchAll();

// $database->getQuery($sql);

$sidebarLinks = '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Sidebar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }

        /* Sidebar styling */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            /* sidebar width */
            overflow-y: auto;
            transition: all 0.3s;
            background-color: #343a40;
            /* dark background */
        }

        .sidebar.minimized {
            width: 50px;
        }

        /* Content styling */
        .content {
            transition: margin-left 0.3s;
            margin-left: 250px;
        }

        /* Sidebar toggle button */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 999;
        }



        /* Menu entries */
        .sidebar .nav-link {
            color: #ddd;
        }

        .sidebar.minimized .menu-text {
            display: none;
        }

        .sidebar.minimized .sidebar-header,
        .sidebar.minimized .nav-link {
            text-align: center;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
                /* sidebar hidden */
            }

            .content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
                cursor: pointer;
            }
        }
    </style>
</head>

<body>

    <div id="mySidebar" class="sidebar">
        <div class="sidebar-header">
            <h3>Sidebar</h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="#">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Contact</a>
            </li>
        </ul>
    </div>

    <div class="content">
        <button class="sidebar-toggle btn btn-dark" onclick="toggleSidebar()">Toggle</button>
        <div class="container">
            <h2>Main Content</h2>
            <p>This is the main content section.</p>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("mySidebar");
            var content = document.querySelector(".content");
            var isMinimized = sidebar.classList.contains('minimized');

            if (window.innerWidth <= 768) {
                // On small screens, toggle visibility
                var sidebarWidth = sidebar.style.marginLeft === "0px" ? "-250px" : "0px";
                sidebar.style.marginLeft = sidebarWidth;
            } else {
                // On large screens, toggle between minimized and full
                if (isMinimized) {
                    sidebar.classList.remove('minimized');
                    content.style.marginLeft = "250px";
                } else {
                    sidebar.classList.add('minimized');
                    content.style.marginLeft = "50px";
                }
            }
        }

        // Adjust layout on window resize
        window.onresize = function(event) {
            var sidebar = document.getElementById("mySidebar");
            var content = document.querySelector(".content");
            if (window.innerWidth > 768) {
                sidebar.style.marginLeft = "0";
                sidebar.classList.remove('minimized');
                content.style.marginLeft = "250px";
            } else if (!sidebar.classList.contains('minimized')) {
                sidebar.style.marginLeft = "-250px";
                content.style.marginLeft = "0";
            }
        };
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>