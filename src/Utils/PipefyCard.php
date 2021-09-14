<?php

namespace Lara\Jarvis\Utils;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\View;

/**
 * Class UploadFile
 * @package App\Enums
 */
class PipefyCard
{
    const ID_PIPE_CARD_CREATE = 'aee2a47a-17fa-41ae-9576-ba01598d5a7a';

    /**
     * @param $list_id
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    public static function new ($list_id, $data)
    {
        $validator = Validator::make(array_merge(['pipe_uuid' => $list_id], $data), [
            'pipe_uuid' => 'required|string',
            'empresa'   => 'required|string',
            'holder'    => 'required|string',
            'number'    => 'required|string',
            'hash'      => 'required|string',
            'image'     => 'required|string',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);

        $url = 'https://app.pipefy.com/graphql/core';

        $token = config('jarvis.pipefy.token');

        if (config('jarvis.pipefy.production')) {
            $pipeUuid           = 'aee2a47a-17fa-41ae-9576-ba01598d5a7a';
            $assigneeIds        = [];
            $attachments        = [];
            $dueDate            = [];
            $fields             = [
                [
                    "fieldUuid"  => "5825317c-dd31-4f05-811e-fc5330963cec",
                    "fieldValue" => $data['empresa']
                ],
                [
                    "fieldUuid"  => "290d53f6-771f-478a-8dcb-6b807c17578a",
                    "fieldValue" => $data['holder']
                ],
                [
                    "fieldUuid"  => "6fd1bc72-91d2-42f5-bd59-ca90e4b01c02",
                    "fieldValue" => $data['number']
                ],
                [
                    "fieldUuid"  => "f57f98df-d489-4c39-b13a-b3ff64bf8521",
                    "fieldValue" => $data['hash']
                ],
                [
                    "fieldUuid"  => "a2c3fec8-77d2-4650-b390-1622a520e948",
                    "fieldValue" => [
                        $data['image']
                        //                        "https://s3.us-east-2.amazonaws.com/cdn-dev.muito.io/card/card.pdf"
                    ]
                ]
            ];
            $labelIds           = [];
            $parentUuids        = [];
            $includeParentCards = false;
            $phaseUuid          = null;
            $throughConnectors  = null;

            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $token])
                ->post($url, array_merge($data, [
                        "operationName" => "createCard",
                        "variables"     => [
                            "pipeUuid"           => $pipeUuid,
                            "assigneeIds"        => $assigneeIds,
                            "attachments"        => $attachments,
                            "fields"             => $fields,
                            "labelIds"           => $labelIds,
                            "parentUuids"        => $parentUuids,
                            "includeParentCards" => $includeParentCards
                        ],
                        "query"         => 'mutation createCard($pipeUuid: ID!, $assigneeIds: [ID], $attachments: [String], $dueDate: DateTime, $fields: [FieldValueInput], $labelIds: [ID], $parentUuids: [ID], $phaseUuid: ID, $throughConnectors: ReferenceConnectorFieldCoreInput, $includeParentCards: Boolean!) {  createCard(input: {pipeUuid: $pipeUuid, phaseUuid: $phaseUuid, assigneeIds: $assigneeIds, attachments: $attachments, dueDate: $dueDate, fields: $fields, labelIds: $labelIds, parentUuids: $parentUuids, throughConnectors: $throughConnectors} ) {   card {     id     uuid     suid     title     url     assignees: lastAssignees {       id       name       avatarUrl       __typename     }     labels {       id       name       color       __typename     }     createdAt     due_date: dueDate     columns {       field {         id: internal_id         __typename       }       name       value       datetime_value       __typename     }     summary {       title       value       __typename     }     current_phase: currentPhase {       id       name       __typename     }     current_phase_age     age     updated_at: updatedAt     parentCards @include(if: $includeParentCards) {       id       __typename     }     __typename   }   __typename }}'
                    ])
                );

            $response->successful();

            return json_decode($response->body());
        }
    }


    public static function sendExample ()
    {
        $card = \App\Models\Card::findOrFail(1);
        $view =
            View::make('cards.layouts.card', ['holder' => $card->holder, 'card_number' => $card->number, 'card_hash' => $card->hash]);
        $pdf  = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);


        $fileUrl = UploadFile::upload(["file" => $pdf->output(Str::uuid() . '.pdf')], "card/$card->id/", "pdf");

        return PipefyCard::new(PipefyCard::ID_PIPE_CARD_CREATE, [
            'empresa' => 'Paggue',
            'holder'  => 'Rodrigo Fraga Oliveira',
            'number'  => '9999 6117 4751 2114',
            'hash'    => 'f7f69340-1de0-4eb8-93e8-cee7899ee0c1',
            'image'   => $fileUrl,
            //            'image'   => 'https://s3.us-east-2.amazonaws.com/cdn-dev.muito.io/card/card.pdf',
        ]);
    }

    private function requestData ()
    {

    }
}
