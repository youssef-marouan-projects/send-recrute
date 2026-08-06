<?php

/**
 * Alternative autoloader for smalot/pdfparser (without Composer)
 */

function requireFilesOfFolder($dir)
{
    foreach (new DirectoryIterator($dir) as $fileInfo) {
        if (!$fileInfo->isDot()) {
            if ($fileInfo->isDir()) {
                requireFilesOfFolder($fileInfo->getPathname());
            } else {
                if (substr($fileInfo->getFilename(), -4) === '.php') {
                    require_once $fileInfo->getPathname();
                }
            }
        }
    }
}

$rootFolder = __DIR__ . '/src/Smalot/PdfParser';

// Load important base files first
require_once $rootFolder . '/Element.php';
require_once $rootFolder . '/PDFObject.php';
require_once $rootFolder . '/Font.php';
require_once $rootFolder . '/Page.php';
require_once $rootFolder . '/Element/ElementString.php';
require_once $rootFolder . '/Encoding/AbstractEncoding.php';

// Load the rest of the library
requireFilesOfFolder($rootFolder);
