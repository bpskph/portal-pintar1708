<?php
// File ini public_html/relasi/vendor/dompdf/dompdf/src/Adapter/CPDF.php

// Fungsi ini, beberapa baris saya jadikan comment aja:
public function stream($filename = "document.pdf", $options = [])
    {
        if (headers_sent()) {
            die("Unable to stream pdf: headers already sent");
        }

        if (!isset($options["compress"])) $options["compress"] = true;
        if (!isset($options["Attachment"])) $options["Attachment"] = true;

        $this->_add_page_text();

        $debug = !$options['compress'];
        $tmp = ltrim($this->_pdf->output($debug));

        // header("Cache-Control: private");
        header("Content-Type: application/pdf");
        // header("Content-Length: " . mb_strlen($tmp, "8bit"));

        $filename = str_replace(["\n", "'"], "", basename($filename, ".pdf")) . ".pdf";
        $attachment = $options["Attachment"] ? "attachment" : "inline";
        //header(Helpers::buildContentDispositionHeader($attachment, $filename));

        echo $tmp;
        flush();
    }
?>