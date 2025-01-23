<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$s3 = new S3Client([
    'version' => 'latest',
    'region' => 'us-east-1',
    'credentials' => [
        'key'    => $_ENV['S3_KEY'],
        'secret' => $_ENV['S3_SECRET'],
    ],
]);

$bucketName = 'somosvfit';

try {
    $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
    $videoFiles = [];
    $continuationToken = null;

    do {
        $params = [
            'Bucket' => $bucketName,
            'MaxKeys' => 1000, 
        ];

        if ($continuationToken) {
            $params['ContinuationToken'] = $continuationToken;
        }

        $result = $s3->listObjectsV2($params);

        if (isset($result['Contents'])) {
            foreach ($result['Contents'] as $object) {
                $key = $object['Key'];

                $extension = pathinfo($key, PATHINFO_EXTENSION);
                if (in_array(strtolower($extension), $videoExtensions)) {
                    $videoFiles[] = $key;
                }
            }
        }

        $continuationToken = $result['NextContinuationToken'] ?? null;
    } while ($continuationToken);

    echo "Total de videos encontrados: " . count($videoFiles) . "\n";
    exportToTxt($videoFiles);
    dd($videoFiles);
} catch (Aws\Exception\AwsException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

function exportToTxt($videoFiles)
{
    $file = 'lista-videos.txt';

    file_put_contents($file, "");
    
    foreach ($videoFiles as $line) {
        file_put_contents($file, $line . PHP_EOL, FILE_APPEND);
    }
    
    echo "Archivo guardado en: " . realpath($file);
}
