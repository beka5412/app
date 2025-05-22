<?php

namespace Backend\Services\PagarMe;

class PagarMe
{
    public $endpoint = '';
    public $account_id = '';
    public $public_key = '';
    public $secret_key = '';

    public function __construct()
    {
        $this->endpoint = env('PAGARME_ENDPOINT');
        $this->account_id = env('PAGARME_ACCOUNT_ID');
        $this->public_key = env('PAGARME_PUBLIC_KEY');
        $this->secret_key = env('PAGARME_SECRET_KEY');
    }
    
    public function creditCard($data)
    {
        $amount = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($data["total"]), 2)))));
        
        $total_seller = $data["total_seller"] ?? 0;
        $total_seller = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($total_seller), 2)))));

        $total_aff = $data["total_aff"] ?? 0;
        $total_aff = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($total_aff), 2)))));

        $rest = $amount - $total_seller - $total_aff;

        $credit_card = [
            "recurrence" => false,
            "installments" => $data["installments"],
            "statement_descriptor" => "ROCKETPAYS",            
        ];
        
        if (($card_id = $data["card_id"] ?? '') && $data["use_saved_card"] == 1) $credit_card["card_id"] = $card_id;
        else $credit_card["card"] = [
            "number" => $data["card_number"],
            "holder_name" => $data["holdername"],
            "holder_document" => $data["customer"]["cpf_cnpj"],
            "exp_month" => (Int) $data["month"],
            "exp_year" => (Int) $data["year"],
            "cvv" => $data["cvv"],
            // "brand" => "Visa",
        
            "billing_address" => [
                "line_1" => "Rua/Av ".($data["customer"]["billing_address"]["street"] ?? '')
                    .", No ".($data["customer"]["billing_address"]["number"] ?? '').", " 
                    .($data["customer"]["billing_address"]["district"] ?? ''),
                "line_2" => $data["customer"]["billing_address"]["complement"] ?? '',
                "zip_code" => $data["customer"]["billing_address"]["zipcode"] ?? '',
                "city" => $data["customer"]["billing_address"]["city"] ?? '',
                "state" => $data["customer"]["billing_address"]["state"] ?? '',
                "country" => "BR"
            ],
            "options" => [
                "verify_card" => true
            ]
        ];

        $payments = [
            [
                "payment_method" => "credit_card",
                "credit_card" => $credit_card
            ]
        ];

        $split = [
            [
                "amount" => $total_seller + $total_aff,
                "recipient_id" => env('PAGARME_SPLIT_ACC1'),
                "type" => "flat",
                "options" => [
                    "charge_processing_fee" => false,
                    "charge_remainder_fee" => true,
                    "liable" => true
                ]
            ],
            [
                "amount" => $rest,
                "type" =>"flat",
                "recipient_id" => env('PAGARME_SPLIT_ACC2'),
                "options" => [
                    "charge_processing_fee" => true,
                    "charge_remainder_fee" => false,
                    "liable" => false
                ]
            ]
        ];

        if ($total_seller > 0 && $rest > 0)
            $payments[0]["split"] = $split;

        $response = $this->request(
            verb: 'POST', 
            url: "/orders",
            headers: [
                "Content-Type" => "application/json"
            ],
            body: json_encode([
                "antifraud_enabled" => false,
                "customer" => [
                    "name" => $data["customer"]["name"],
                    "email" => $data["customer"]["email"],
                    // "birthdate" => "01/17/1953",
                    "document" => $data["customer"]["cpf_cnpj"],
                    "type" => strlen($data["customer"]["cpf_cnpj"]) > 14 ? "company" : "individual",
                    "phones" => [
                        // "home_phone" => [
                        //     "country_code" => "55",
                        //     "number" => "680349071",
                        //     "area_code" => "11"
                        // ],
                        "mobile_phone" => [
                            "country_code" => "55",
                            "number" => substr($data["customer"]["phone"], 2, strlen($data["customer"]["phone"])),
                            "area_code" => substr($data["customer"]["phone"], 0, 2)
                        ]
                    ]
                ],
                "closed" => false,
                "items" => [
                    [
                        "amount" => $amount,
                        "code" => $data["product"]->id,
                        "description" => $data["product"]->name,
                        "quantity" => 1
                    ]
                ],
                "payments" => $payments
            ])
        );

        return $response;
    }
    public function subscription($data)
    {
        $amount = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($data["total"]), 2)))));

        $payload = [
            "metadata" => ["transaction_id" => $data["transaction_id"]],
            "payment_method" => "credit_card",
            "interval" => $data["plan"]["interval"] ?? "month",
            "interval_count" => $data["plan"]["interval_count"] ?? 1, // 1 = mensal | 3 = trimestral | 6 = semestral
            "billing_type" => "prepaid",
            "installments" => $data["installments"],
            "items" => [
                [
                    "pricing_scheme" =>
                    [
                        "scheme_type" => "Unit",
                        "price" => $amount,
                    ],
                    "description" => "Descrição",
                    "quantity" => 1
                ]
            ],
            "metadata" => [
                "id" => "my_subscription_id"
            ],
            "pricing_scheme" => [
                "scheme_type" => "Unit",
                "description" => "Descrição",
                "price" => intval(str_replace(".", "", strval(number_format(doubleval($data["total"]), 2)))),
            ],
            "quantity" => null,
            "currency" => "BRL",
        ];

        if ($data["use_saved_card"] == 0)
        {
            $payload = $payload + 
            [
                "card" => 
                [
                    "number" => $data["card_number"],
                    "holder_name" => $data["holdername"],
                    "holder_document" => $data["customer"]["cpf_cnpj"],
                    "exp_month" => (Int) $data["month"],
                    "exp_year" => (Int) $data["year"],
                    "cvv" => $data["cvv"],
                
                    "billing_address" => 
                    [
                        "line_1" => "Rua/Av ".($data["customer"]["billing_address"]["street"] ?? '')
                            .", No ".($data["customer"]["billing_address"]["number"] ?? '').", " 
                            .($data["customer"]["billing_address"]["district"] ?? ''),
                        "line_2" => $data["customer"]["billing_address"]["complement"] ?? '',
                        "zip_code" => $data["customer"]["billing_address"]["zipcode"] ?? '',
                        "city" => $data["customer"]["billing_address"]["city"] ?? '',
                        "state" => $data["customer"]["billing_address"]["state"] ?? '',
                        "country" => "BR"
                    ],
                    "options" => [
                        "verify_card" => true
                    ]
                ],
                
                "customer" => [
                    "name" => $data["customer"]["name"],
                    "email" => $data["customer"]["email"],
                    "document" => $data["customer"]["cpf_cnpj"],
                    "type" => strlen(preg_replace('/\D/', '', $data["customer"]["cpf_cnpj"])) > 11 ? "company" : "individual",
                    "phones" => [
                        "mobile_phone" => [
                            "country_code" => "55",
                            "number" => substr($data["customer"]["phone"], 2, strlen($data["customer"]["phone"])),
                            "area_code" => substr($data["customer"]["phone"], 0, 2)
                        ]
                    ]
                ]
            ];
        }
        
        else
        {
            $payload = $payload + ["card_id" => $data["card_id"], "customer_id" => $data["customer_id"]];
        }

        $response = $this->request(
            verb: 'POST', 
            url: "/subscriptions",
            headers: [
                "Content-Type" => "application/json"
            ],
            body: json_encode($payload)
        );

        return $response;
    }

    public function billet($data)
    {
        $amount = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($data["total"]), 2)))));
        
        $total_seller = $data["total_seller"] ?? 0;
        $total_seller = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($total_seller), 2)))));

        $total_aff = $data["total_aff"] ?? 0;
        $total_aff = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($total_aff), 2)))));

        $rest = $amount - $total_seller - $total_aff;

        $payments = [
            [
                "payment_method" => "boleto",
                "boleto" => [
                    "instructions" => "Pagar até o vencimento",
                    "due_at" => date("Y-m-d H:i:s", strtotime(today() . "+ 3 days")),
                    "document_number" => time(),
                    "type" => "DM" // DM (Duplicata Mercantil) e BDP (Boleto de proposta)
                ]                  
            ]
        ];

        $split = [
            [
                "amount" => $total_seller + $total_aff,
                "recipient_id" => env('PAGARME_SPLIT_ACC1'),
                "type" => "flat",
                "options" => [
                    "charge_processing_fee" => false,
                    "charge_remainder_fee" => true,
                    "liable" => true
                ]
            ],
            [
                "amount" => $rest,
                "type" =>"flat",
                "recipient_id" => env('PAGARME_SPLIT_ACC2'),
                "options" => [
                    "charge_processing_fee" => true,
                    "charge_remainder_fee" => false,
                    "liable" => false
                ]
            ]
        ];

        if ($total_seller > 0 && $rest > 0)
            $payments[0]["split"] = $split;

        $response = $this->request(
            verb: 'POST', 
            url: "/orders",
            headers: [
                "Content-Type" => "application/json"
            ],
            body: json_encode([
                "customer" => [
                    "name" => $data["customer"]["name"],
                    "email" => $data["customer"]["email"],
                    // "birthdate" => "01/17/1953",
                    "document" => $data["customer"]["cpf_cnpj"],
                    "type" => strlen($data["customer"]["cpf_cnpj"]) > 14 ? "company" : "individual",
                    "phones" => [
                        // "home_phone" => [
                        //     "country_code" => "55",
                        //     "number" => "680349071",
                        //     "area_code" => "11"
                        // ],
                        "mobile_phone" => [
                            "country_code" => "55",
                            "number" => substr($data["customer"]["phone"], 2, strlen($data["customer"]["phone"])),
                            "area_code" => substr($data["customer"]["phone"], 0, 2)
                        ]
                    ]
                ],
                "closed" => false,
                "items" => [
                    [
                        "amount" => $amount, // intval(str_replace(".", "", strval(number_format(doubleval($amount), 2)))),
                        "code" => $data["product"]->id,
                        "description" => $data["product"]->name,
                        "quantity" => 1
                    ]
                ],
                "payments" => $payments
            ])
            // json_encode([
            //     "customer" => [
            //         "name" => "Seu madruga",
            //         "email" => "seumadruga@gmail.com",
            //         "birthdate" => "01/17/1953",
            //         "document" => "17127592489",
            //         "type" => "individual",
            //         "phones" => [
            //             "home_phone" => [
            //                 "country_code" => "55",
            //                 "number" => "680349071",
            //                 "area_code" => "11"
            //             ],
            //             "mobile_phone" => [
            //                 "country_code" => "55",
            //                 "number" => "680349070",
            //                 "area_code" => "11"
            //             ]
            //         ]
            //     ],
            //     "closed" => false,
            //     "items" => [
            //         [
            //             "amount" => 150,
            //             "code" => 215,
            //             "description" => "Chaveiro do Tesseract2",
            //             "quantity" => 1
            //         ]
            //     ],
            //     "payments" => [
            //         [
            //             "payment_method" => "boleto",
            //             "boleto" => [
            //                 "instructions" => "Pagar",
            //                 "due_at" => "2023-02-20T00:00:00Z",
            //                 "document_number" => "47683264474",
            //                 "type" => "DM"
            //             ]
            //         ]
            //     ]
            // ])
        );

        return $response;
    }

    public function pix($data)
    {
        $amount = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($data["total"]), 2)))));

        $total_seller = $data["total_seller"] ?? 0;
        $total_seller = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($total_seller), 2)))));

        $total_aff = $data["total_aff"] ?? 0;
        $total_aff = intval(str_replace(",", "", str_replace(".", "", strval(number_format(doubleval($total_aff), 2)))));

        $rest = $amount - $total_seller - $total_aff;

        $payments = [
            [
                "payment_method" => "pix",
                "pix" => [
                    "expires_in" => "52134613",
                    "additional_information" => [
                        [
                            "name" => "Quantidade",
                            "value" => "1"
                        ]
                    ]
                ]                     
            ]
        ];

        $split = [
            [
                "amount" => $total_seller + $total_aff,
                "recipient_id" => env('PAGARME_SPLIT_ACC1'),
                "type" => "flat",
                "options" => [
                    "charge_processing_fee" => false,
                    "charge_remainder_fee" => true,
                    "liable" => true
                ]
            ],
            [
                "amount" => $rest,
                "type" =>"flat",
                "recipient_id" => env('PAGARME_SPLIT_ACC2'),
                "options" => [
                    "charge_processing_fee" => true,
                    "charge_remainder_fee" => false,
                    "liable" => false
                ]
            ]
        ];

        if ($total_seller > 0 && $rest > 0)
            $payments[0]["split"] = $split;
       
        $response = $this->request(
            verb: 'POST', 
            url: "/orders",
            headers: [
                "Content-Type" => "application/json"
            ],
            body: json_encode([
                "customer" => [
                    "name" => $data["customer"]["name"],
                    "email" => $data["customer"]["email"],
                    // "birthdate" => "01/17/1953",
                    "document" => $data["customer"]["cpf_cnpj"],
                    "type" => strlen($data["customer"]["cpf_cnpj"]) > 14 ? "company" : "individual",
                    "phones" => [
                        // "home_phone" => [
                        //     "country_code" => "55",
                        //     "number" => "680349071",
                        //     "area_code" => "11"
                        // ],
                        "mobile_phone" => [
                            "country_code" => "55",
                            "number" => substr($data["customer"]["phone"], 2, strlen($data["customer"]["phone"])),
                            "area_code" => substr($data["customer"]["phone"], 0, 2)
                        ]
                    ]
                ],
                "closed" => false,
                "items" => [
                    [
                        "amount" => $amount,
                        "code" => $data["product"]->id,
                        "description" => $data["product"]->name,
                        "quantity" => 1
                    ]
                ],
                "payments" => $payments
            ])
        );

        return $response;
    }

    public function getLastCharge($params)
    {
        $response = $this->request(
            verb: 'GET', 
            url: "/charges",
            headers: [
                "Content-Type" => "application/json"
            ],
            body: http_build_query($params)
        );

        return $response;
    }

    public function getCards($customer_id)
    {
        $response = $this->request(
            verb: 'GET', 
            url: "/customers/$customer_id/cards",
            headers: [
                "Content-Type" => "application/json"
            ],
            body: null
        );

        return $response;
    }

    public function addCard($customer_id, $card)
    {
        $response = $this->request(
            verb: 'POST', 
            url: "/customers/$customer_id/cards",
            headers: [
                "Content-Type" => "application/json"
            ],
            body: json_encode($card)
        );

        return $response;
    }

    public function cancelSubscription($subscription_id)
    {
        $response = $this->request(
            verb: 'DELETE', 
            url: "/subscriptions/$subscription_id",
            headers: [
                "Content-Type" => "application/json"
            ]
        );

        return $response;
    }

    private function request($verb, $url, $headers=[], $body="")
    {
        $headers = array_to_header($headers); 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->endpoint.$url);
        curl_setopt($curl, CURLOPT_USERPWD, $this->secret_key.":");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($body)) curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $verb);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}