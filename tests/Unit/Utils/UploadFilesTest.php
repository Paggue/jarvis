<?php

namespace Lara\Jarvis\Tests\Unit\Utils;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Utils\UploadFile;

class UploadFilesTest extends TestCase
{
    /**
     * @test
     */
    public function can_upload_and_delete_a_base64_file ()
    {
        // VERIFYING IF BUCKET HAS UPLOADED FILE

        $file         = TestCase::BASE64_EXAMPLE;
        $data["file"] = $file;

        $folder = 'random_name/';

        $uploaded_file_url = UploadFile::upload($data, $folder);

        $s3 = App::make('aws')->createClient('s3');

        $array = explode('/', $uploaded_file_url);
        $key   = end($array);
        $key   = explode('%', $key)[0];

        $response = $s3->doesObjectExist(env('AWS_BUCKET'), $folder . $key . '=.jpeg');

        $this->assertEquals(true, $response);

        // instantly deleting file so it doesnt leave files from tests on the s3 bucket

        UploadFile::deleteUpload($uploaded_file_url, $folder);

        $response = $s3->doesObjectExist(env('AWS_BUCKET'), $folder . $key . '=.jpeg');

        $this->assertEquals(false, $response);
    }

    /**
     * @test
     */
    public function can_upload_and_delete_a_pdf_file ()
    {
        // VERIFYING IF BUCKET HAS UPLOADED FILE

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML('<h1>Test</h1>');

        $extension = "pdf";

        $data["file"] = $pdf->output(Str::uuid() . ".$extension");

        $folder = 'random_name/';

        $uploaded_file_url = UploadFile::upload($data, $folder, $extension);

        $s3 = App::make('aws')->createClient('s3');

        $array = explode('/', $uploaded_file_url);
        $key   = end($array);
        $key   = explode('%', $key)[0];

        $response = $s3->doesObjectExist(env('AWS_BUCKET'), $folder . $key . "=.$extension");

        $this->assertEquals(true, $response);

        // instantly deleting file so it doesnt leave files from tests on the s3 bucket

        UploadFile::deleteUpload($uploaded_file_url, $folder);

        $response = $s3->doesObjectExist(env('AWS_BUCKET'), $folder . $key . "=.$extension");

        $this->assertEquals(false, $response);
    }
}
