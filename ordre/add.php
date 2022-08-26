<?php

require_once __DIR__ . '/../../../../wp-load.php';
require_once __DIR__ . '/../../../../vendor/autoload.php';

use Symfony\Component\HttpFoundation\File\UploadedFile;
use AcMarche\Conseil\ConseilConstantes;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

$filesystem = new Filesystem();
$request = Request::createFromGlobals();
$fileName = $request->request->get('file_name');
/**
 * @var UploadedFile $file
 */
$file = $request->files->get('file_field');

if (!$file || !$fileName) {
    echo json_encode(['error' => 'Fichier non envoyé '.$fileName], JSON_THROW_ON_ERROR);
    exit();
}

try {
    $filesystem->rename($file->getPathname(), ConseilConstantes::ORDRE_DIRECTORY.$fileName);
    echo json_encode(['result' => 'ok']);
} catch (IOException $IOException) {
    echo json_encode(['error' => 'Impossible de renommer le fichier '.$IOException->getMessage()], JSON_THROW_ON_ERROR);
}
