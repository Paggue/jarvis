<?php


namespace Lara\Jarvis\Utils;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class UploadFile
 * @package App\Enums
 */
class TrelloCard
{
    const ID_LIST_SUPORTE_CLIENTES = '60c2247ba051766ae78b2c4c';
    const ID_LIST_FALHAS           = '60c2273871f8316a85ac5b32';

    const LABEL_EMERGENCY = '60c2247bc82461383079dd14';

    /**
     * @param $list_id
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public static function new ($list_id, $data)
    {
        $return = null;

        $validator = Validator::make(array_merge(['list_id' => $list_id], $data), [
            'list_id' => 'required|string',
            'name'    => 'required|string',
            'desc'    => 'required|string',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);

        $url = 'https://api.trello.com/1/cards';


        $key_trello   = config('jarvis.trello.key');
        $token_trello = config('jarvis.trello.token');

        $url_params = "?key={$key_trello}&token={$token_trello}&idList={$list_id}";

        $url = $url . $url_params;

        if (config('jarvis.trello.production')) {

            $response = Http::post($url, array_merge($data, [
                    'idLabels' => data_get($data, 'label_ids'),
                ])
            );

            $response->successful();

            $return = $response->body();
        }

        return App::environment('testing') ? $return : json_decode($return);
    }

    public static function sendExample ()
    {
        return TrelloCard::new(TrelloCard::ID_LIST_SUPORTE_CLIENTES, [
            'name' => '[Pré-cadastro] - Empresa Fantasia Atakadão',
            'desc' => "**Empresa Fantasia Atakadão**
                           \n- **ID**: 21825-155ds-sd45sd-584
                           \n- **Nome do cliente**: Super mercado Mais barato só amanhã"
        ]);
    }
}
