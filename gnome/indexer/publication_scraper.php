<?php


class PublicationImport
{
    public $domainRef = null;

    public function setDomainRef($url)
    {
        $tempUrl = parse_url($url);
        $this->domainRef = $tempUrl["scheme"] . '://' . $tempUrl["host"];
        return $this;
    }

    public function fetchUrl($url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 50,
            CURLOPT_BUFFERSIZE => 120000000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                'cache-control: no-cache'
            ],
        ]);

        $resp = curl_exec($curl);
        $info = curl_getinfo($curl);
        $error = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        return [$resp, $info, $error, $errorMessage];
    }

    public function downloadFile($url, $path, $directory)
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $file = fopen($url, 'rb');
        if ($file) {
            $newf = fopen($path, 'wb');
            if ($newf) {
                while (!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
                fclose($newf);
            }
            fclose($file);
        }
    }

    public function fetchImages(array $imgs, $publication)
    {
        foreach ($imgs as $val) {
            $newfile = str_replace("/", "_", $val);
            $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'publications' . DIRECTORY_SEPARATOR . $publication;
            $this->downloadFile($this->domainRef . '/' . $val, $path . DIRECTORY_SEPARATOR . $newfile, $path);
        }
    }
}

if (isset($_POST['scrapeRules'], $_POST['url'], $_POST['publication_id'])) {
    $isTable = isset($_POST['isTable']);
    $htmlResponse = '';
    $tablesCount = 0;
    $publicationId = strtoupper($_POST['publication_id']);

    $publicationImport = new PublicationImport();
    list($resp, $info, $error, $errorMessage) = $publicationImport->setDomainRef($_POST['url'])->fetchUrl($_POST['url']);

    if ($error) {
        echo "<div class='error align-center'> Scraper error: $errorMessage </div>";
    } else {
        $dom = new IvoPetkov\HTML5DOMDocument();
        $dom->loadHTML($resp, $dom::ALLOW_DUPLICATE_IDS);

        if ($_POST['scrapeRules'] === 'FOMK') {
            $tables = $dom->querySelectorAll('[id="collapsible_report"]');

            foreach ($tables as $i => $table) {
                $htmlResponse .= "<a name='tab_$i'>" . $table->outerHTML;
                $tablesCount++;
            }

            $dom->loadHTML($htmlResponse, $dom::ALLOW_DUPLICATE_IDS);
            $tags = $dom->querySelectorAll('img');
            $imgs = [];

            $path = "/media/images/publications/$publicationId/";

            foreach ($tags as $tag) {
                $tag->parentNode->setAttribute('class', 'responsive-image');
                $oldSrc = $tag->getAttribute('src');

                $re = '/^(?:\S+\.\S+?\/|\/)?(\??\S+)$/mi';
                preg_match($re, $oldSrc, $match);

                $newSrc = str_replace("/", "_", explode("?", $match[1])[0]);
                $tag->setAttribute('src', $path . $newSrc);
                $tag->setAttribute('data-src', $oldSrc);
                $imgs[] = explode("?", $match[1])[0];
            }

            $publicationImport->fetchImages(array_unique($imgs), $publicationId);

            $htmlResponse = $dom->saveHTML();
        } elseif ($_POST['scrapeRules'] === 'body') {
            $htmlResponse = $dom->saveHTML();
        } else {
            $htmlResponse = $resp;
        }

        echo $isTable ? "<table>$htmlResponse</table>" : $htmlResponse;
    }
    exit();
}


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Web Scrapper</title>
    <!-- <link rel="stylesheet" href="/css/scraper.css"> -->
    <!-- <link rel="stylesheet" href="/css/main.css"> -->
    <!-- <script src="/js/lib/jquery/jquery.min.js"></script> -->
</head>

<body>


    <form method="post" id="scrape-form" action="publication_scraper.php">
        <input type="hidden" name="publication_id" value="<?= $_GET['publication_id'] ?>">

        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Web Scraper v1.2</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-4">
                    <strong>Directions</strong>
                    <ol>
                        <li>Select the "Format Source"</li>
                        <li>Enter a url for Target URL</li>
                        <li>Then click "Get Content".</li>
                    </ol>
                </div>
                <div class="col-8">
                    <strong>Format Source</strong>
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="FOMK" name="scrapeRules" value="FOMK" checked="checked">
                            <label class="form-check-label" for="FOMK">Future Of Mankind</label><br>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="url" class=""><strong>Target URL</strong></label>
                        <input type="url" name="url" id="url" class="form-control" placeholder="https://example.com" pattern="[Hh][Tt][Tt][Pp][Ss]?:\/\/(?:(?:[a-zA-Z\u00a1-\uffff0-9]+-?)*[a-zA-Z\u00a1-\uffff0-9]+)(?:\.(?:[a-zA-Z\u00a1-\uffff0-9]+-?)*[a-zA-Z\u00a1-\uffff0-9]+)*(?:\.(?:[a-zA-Z\u00a1-\uffff]{2,}))(?::\d{2,5})?(?:\/[^\s]*)?" required />
                    </div>
                </div>
            </div>
            <div class="row g-3">
            <div class="col-12">
                <div id="scraperResponse" class="grid-container">

                </div>
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="paste-back-btn" class="btn btn-success">Paste
                back</button>

            <input type="submit" name="submitter" id="submit-btn" value="Get Content" class="btn btn-primary" />

            <button class="btn btn-primary d-none" id="submit-btn-load" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Loading...
            </button>

        </div>
    </form>






    <!-- your content here... -->


    <style>
        .toEnumerate {
            background-color: rgb(255, 255, 102);
        }
    </style>
    <script>
        // var currentTagets = [];
        // var currentTarget = null;
        var currentType = null;
        // var formatType = 'FOMK';

        var safe = ["body", "html", "hr", "br"];
        var bubble = ["tbody", "tfoot", "thead", "tr", "td"];

        sectionSelectHandler = e => {

            if (safe.indexOf(e.target.tagName.toLowerCase()) != -1) {
                return;
            }

            // bubble to parent
            if (bubble.indexOf(e.target.tagName.toLowerCase()) != -1) {
                let temp = e.target;
                do {
                    temp = temp.parentNode;
                }
                while (bubble.indexOf(temp.tagName.toLowerCase()) != -1)

                if (temp.classList.contains('toEnumerate')) {
                    temp.classList.remove('toEnumerate');
                } else {
                    temp.classList.add('toEnumerate');
                }

                // console.log(e.target.tagName);
                return;
            }


            // e.target.classList.add('toEnumerate');
            if (e.target.classList.contains('toEnumerate')) {
                e.target.classList.remove('toEnumerate');
            } else {
                e.target.classList.add('toEnumerate');
            }

            currentType = e.target.tagName.toLowerCase()

            return;

        }

        // global
        areaBody = document.querySelector('#scraperResponse');
        areaBody.addEventListener('click', sectionSelectHandler, false);
    </script>
</body>

</html>