<?php


use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
require $_SERVER['DOCUMENT_ROOT'].'/aws/vendor/autoload.php';




function uploadtoS3($filepathtoUpload,$s3Path){
    
    $s3 = S3Client::factory([
        
        
        'region'=>'us-east-1',
        'bucket'=>'himirror-b2b',
        'version'=>'latest',
        'credentials'=> array(
            'key'=>'AKIAIIMFT5LXWASQ4HQQ',
            'secret'=>'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',
        ),
        
    ]);
    try {
    $tmp_file_path = $filepathtoUpload;
    $result = $s3->putObject([
        'Bucket' => 'himirror-b2b',
        'Key'=>$s3Path,
            'Body'=>fopen($tmp_file_path, 'rb'),
        'ACL'    => 'public-read'
    ]);

    unlink($tmp_file_path);
    return $result['ObjectURL'] . PHP_EOL;
} catch (S3Exception $e) {
    return "";
}
}
?>