<?php


namespace Lara\Jarvis\Utils;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class UploadFile
 * @package App\Enums
 */
class FetchDocument
{
    /**
     * @return mixed
     * @throws ValidationException
     */
    public static function get ($data)
    {
        $validator = Validator::make($data, [
            "document" => "required|cnpj",
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);

        $document = Helpers::sanitizeString($data['document']);

        $clientUrl = "https://api.cnpja.com.br/companies/" . $document;

        $response = Http::withHeaders([
            'Authorization' => config('jarvis.cnpja_api_key'),
        ])->get($clientUrl);

        $response->successful();

        return json_decode($response->body());
    }
}
