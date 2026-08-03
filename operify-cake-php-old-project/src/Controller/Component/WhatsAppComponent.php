<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Http\Client;

class WhatsAppComponent extends Component
{



    public function feeapprovalwhatsappmessage($company_name = null, $to = null, $machine_name = null, $assigned = null, $date = null, $time = null, $remarks = null)
    {
        // $to = "917691017681";
        $apiUrl = 'https://api.shoponcell.com/Whatsapp/v1.0/Template/send/message/c4fcf895-4515-4aef-bd40-ea63caeddaa3';
        // API key
        $apiKey = 'f110b1d8afb21331127a9e372384980955951ce5b8401e657128fee0220fb638489f59368abb23bd';
        // JSON payload
        $data = [
            'campaignName' => 'operify_machine_maintenance_request',
            'destination' => (string) $to,
            'templateParams' => [
                (string) $company_name,
                (string) $machine_name,
                (string) $assigned,
                (string) $date,
                (string) $time,
                (string) $remarks
            ],
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n" .
                    "Authorization: Bearer $apiKey\r\n" .
                    "Another-Header: HeaderValue", // Add more headers as needed
                'method' => 'POST',
                'content' => json_encode($data),
                'follow_location' => false,
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($apiUrl, false, $context);
        // pr($response);
        // die;
        return $response;
    }


}
