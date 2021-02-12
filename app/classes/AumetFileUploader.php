<?php

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;

class AumetFileUploader
{
    const AWS_ACCESS_KEY = "AKIAQQRZF2VNF2NDLC7Z";
    const AWS_SECRET_ACCESS_KEY = "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";
    public static function upload($destination, $arrFileParams, $saveFileName)
    {
        $fileName = $arrFileParams['name'];
        $objResult = new stdClass();
        $objResult->isError = false;
        $objResult->isUploaded = false;
        $objResult->fileLink = null;
        $objResult->filePath = null;
        $objResult->data = null;
        $objResult->fileName = $fileName;
        if ($fileName != "" && $fileName != null) {
            $fileTmpPath = $arrFileParams['tmp_name'];
            $fileSize = $arrFileParams['size'];
            $fileType = $arrFileParams['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $targetFileName = "$saveFileName.$fileExtension";
            if ($destination == "files") {
                $f3 = \Base::instance();
                $targetFilePath = $f3->get('uploadDIR') . $targetFileName;
                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    $objResult->isError = false;
                    $objResult->isUploaded = true;
                    $objResult->filePath = $targetFilePath;
                    $objResult->fileName = $fileName;
                    $objResult->fileLink = "/files/uploads/$targetFileName";
                } else {
                    $objResult->isError = true;
                    $objResult->error = "move_uploaded_file failed";
                }
            } else if ($destination == "s3") {
                try {
                    $s3Client = new S3Client([
                        'region' => 'us-west-1',
                        'version' => 'latest',
                        'credentials' => [
                            'key'    => AumetFileUploader::AWS_ACCESS_KEY,
                            'secret' => AumetFileUploader::AWS_SECRET_ACCESS_KEY,
                        ],
                    ]);
                    $result = $s3Client->putObject([
                        'Bucket' => 'aumetapps',
                        'Key'    => 'mp/uploads/' . $targetFileName,
                        'SourceFile' => $fileTmpPath
                    ]);
                    $objResult->isError = false;
                    $objResult->isUploaded = true;
                    $objResult->fileLink = "https://d2qyez1diqag7p.cloudfront.net/mp/uploads/$targetFileName";
                    $objResult->filePath = $result['ObjectURL'];
                    $objResult->data = $result;
                } catch (Exception $e) {
                    $objResult->error = $e->getMessage();
                    $objResult->isError = true;
                }
            } else {
                $objResult->error = "Unknown destination";
                $objResult->isError = true;
            }
        }
        return $objResult;
    }
}
