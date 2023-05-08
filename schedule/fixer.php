<?php

function requireFilesOfFolder($dir)
{
    foreach (new DirectoryIterator($dir) as $fileInfo) {
        if (!$fileInfo->isDot()) {
            if ($fileInfo->isDir()) {
                requireFilesOfFolder($fileInfo->getPathname());
            } else {
                require_once $fileInfo->getPathname();
            }
        }
    }
}

$rootFolder = __DIR__.'/src/Smalot/PdfParser';

// Manually require files, which can't be loaded automatically that easily.
require_once $rootFolder.'/Element.php';
require_once $rootFolder.'/PDFObject.php';
require_once $rootFolder.'/Font.php';
require_once $rootFolder.'/Page.php';
require_once $rootFolder.'/Element/ElementString.php';
require_once $rootFolder.'/Encoding/AbstractEncoding.php';

/*
 * Load the rest of PDFParser files from /src/Smalot/PDFParser
 * Dont worry, it wont load files multiple times.
 */
requireFilesOfFolder($rootFolder);