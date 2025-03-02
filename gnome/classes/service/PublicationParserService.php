<?php

namespace gnome\classes\service;

use gnome\classes\DBConnection as DBConnection;
use PDO;
use DOMDocument;

class PublicationParserService extends DBConnection
{


    public function parseTextFromTable($id) {
        $sql = "SELECT publication_id, raw_html FROM publications WHERE publication_id = :publication_id";
        $pdoc = $this->dbc->prepare($sql);
        $pdoc->execute([':publication_id' => $id]);

        while ($row = $pdoc->fetch(PDO::FETCH_ASSOC)) {
            list($englishText, $germanText) = $this->extractTextsFromHtml($row['raw_html']);

            $insertStmt = $this->dbc->prepare("
                INSERT INTO publication_text_content (publication_id, english_text, german_text)
                VALUES (:publication_id, :english_text, :german_text)
                ON DUPLICATE KEY UPDATE 
                english_text = VALUES(english_text), 
                german_text = VALUES(german_text)
            ");
            $insertStmt->execute([
                ':publication_id' => $row['publication_id'],
                ':english_text' => $englishText,
                ':german_text' => $germanText
            ]);
        }
    }

    private function extractTextsFromHtml($htmlBlob) {
        $englishText = '';
        $germanText = '';

        $doc = new DOMDocument();
        libxml_use_internal_errors(true); 
 
        $doc->loadHTML('<?xml encoding="UTF-8">' . $htmlBlob);
        libxml_clear_errors();

        $tables = $doc->getElementsByTagName('table');

        if ($tables->length > 0) {
            // ignore the red line
            $mainRows = $tables->item(0)->getElementsByTagName('tr');
            foreach ($mainRows as $row) {
                /** @disregard [OPTIONAL CODE] [OPTIONAL DESCRIPTION] */
                $cells = $row->getElementsByTagName('td');
                if ($cells->length == 2) {
                    $englishText .= $this->extractTextFromNestedElements($cells->item(0)) . " ";
                    $germanText .= $this->extractTextFromNestedElements($cells->item(1)) . " ";
                }
            }
        }
        return [$englishText, $germanText];
    }

    private function extractTextFromNestedElements($element) {
        $text = '';
        foreach ($element->childNodes as $child) {
            if ($child->nodeType == XML_TEXT_NODE) {
                $text .= $child->nodeValue;
            } else if ($child->nodeType == XML_ELEMENT_NODE) {
                $text .= $this->extractTextFromNestedElements($child);
            }
        }
        return $text;
    }
}
