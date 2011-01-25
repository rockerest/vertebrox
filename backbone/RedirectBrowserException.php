<?php

    class RedirectBrowserException extends Exception {

        public function __construct($url) {
            header("Location: {$url}");
            exit;
        }

    }

?>