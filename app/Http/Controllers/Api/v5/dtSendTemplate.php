<?php

namespace App\Http\Controllers\Api\v5;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Twilio\Rest\Client;
use DB;
use Carbon\Carbon;


class dtSendTemplate extends Controller
{
    public function dtSendTemplate(Request $request)
    {
        try {
            // $token = $request->bearerToken();

            if ($request->senderName === 'DRAW') {



                $request->mobileNo = Controller::BlockSQLInjection($request->mobileNo);
                if ($request->mobileNo == '' || $request->mobileNo == null || $request->mobileNo == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please provide a mobile number.', 'error' => 'Please provide a mobile number.'];
                    goto returnFVI;
                }

                // $request->message = Controller::BlockSQLInjection($request->message);
                // if ($request->message == '' || $request->message == null || $request->message == 'null') {
                //     $response = ['status' => 'failed', 'message' => 'Please provide a valid message!', 'error' => 'Invalid message provided.'];
                //     goto returnFVI;
                // }

                $request->templateName = $request->templateName;
                if ($request->templateName == '' || $request->templateName == null || $request->templateName == 'null') {
                    $response = ['status' => 'failed', 'message' => 'Please provide a valid template name.', 'error' => 'Please provide a valid template name.'];
                    goto returnFVI;
                }

                $request->language = Controller::BlockSQLInjection($request->language);
                if ($request->language == '' || $request->language == null || $request->language == 'null') {
                    $response = [
                        'status' => 'failed',
                        'message' => 'Please provide a valid language.',
                        'error' => 'Please provide a valid language.'
                    ];
                    goto returnFVI;
                }





                $templaTeNames = [
                    "national_draw_verification",
                    "nd_prize_details_v4",
                    "pick_up_at_the_store_collected_v2",
                    "final_shipping_order_confirm_v3",
                    "final_pickup_at_the_store__order_confirm_v11",
                    "out_of_delivery_tracking_v2",
                    "shipping_order_delivered",
                    "winner_whatsapp_template_2g_v7_final_v2",
                    "rejected_withdrwal_template_v3",
                    "ticket_purchase_template_final_v3",
                    "ticket_purchase_customer_template_v3"
                ];

                if (!in_array($request->templateName, $templaTeNames)) {
                    $response = ['status' => 'failed', 'message' => 'The Template not found.', 'error' => 'The Template not found.'];
                    goto returnFVI;
                }
                $ddRequest = [];
                if ($request->templateName === 'national_draw_verification') {

                    if ($request->templateBodyParam == '' || $request->templateBodyParam == null || $request->templateBodyParam == 'null') {
                        $response = ['status' => 'failed', 'message' => 'Please provide the template body parameters.', 'error' => 'Please provide the template body parameters.'];
                        goto returnFVI;
                    }


                    $message = 'Your National Draw code is ' . $request->templateBodyParam[0] . '. Please don\'t share with anyone.';

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ]
                            ]
                        ]
                    ];
                } else if ($request->templateName === 'nd_prize_details_v4') {

                    $message = 'Thanks for participating in the National Draw!! To know more about the National Draw Prize Details, Click on the image to Download the catalogue👆';

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ]
                            ],
                            "header" => [
                                "type" => "DOCUMENT",
                                "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/10f84bc3-276a-440d-99f7-e1b28ee96e5e.pdf",
                                "filename" => "10f84bc3-276a-440d-99f7-e1b28ee96e5e.pdf"
                            ]
                        ]
                    ];
                } else if ($request->templateName === 'pick_up_at_the_store_collected_v2') {

                    $message = '🌿Thank you for shopping by our warehouse and picking up your product!📦  Your support means the world to us. ♻️ Let\'s keep moving forward together for a greener, more sustainable world! 🌍 #Sustainability#ThankYou🙏';

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ]
                            ],
                            "header" => [
                                "type" => "IMAGE",
                                "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/74ddff36-9882-4170-8f96-e0db8afac8ec.jpeg",
                                "filename" => "74ddff36-9882-4170-8f96-e0db8afac8ec.jpeg"
                            ],
                            "buttons" => [
                                [
                                    "type" => "URL"
                                ]
                            ]
                        ]
                    ];
                } else if ($request->templateName === 'final_shipping_order_confirm_v3') {

                    $message = 'Hi ' . $request->templateBodyParam[0] . ' Thankyou for placing an order with National Draw!!! We have received your order on ' . $request->templateBodyParam[1] . ' and your Order ID ' . $request->templateBodyParam[2] . ', Total pen count ' . $request->templateBodyParam[3] . '. we are getting it ready. ✨We\`ll let you know when it is shipped and on its way! 🚚 Thank you, Team National Draw';

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ],
                                "header" => [
                                    "type" => "IMAGE",
                                    "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/0b2165a2-0304-41d5-a33e-40168b2f7812.jpeg",
                                    "filename" => "0b2165a2-0304-41d5-a33e-40168b2f7812.jpeg"
                                ],
                                "buttons" => $request->buttons
                            ],

                            // [
                            //     [
                            //         "type" => "URL"
                            //     ]
                            // ]
                        ]
                    ];
                } else if ($request->templateName === 'final_pickup_at_the_store__order_confirm_v11') {

                    // $message = 'Hi ' . $request->templateBodyParam[0] . ' Thankyou for placing an order with National Draw!!! We have received your order on ' . $request->templateBodyParam[1] . ' and your Order ID ' . $request->templateBodyParam[2] . ', Total pen count ' . $request->templateBodyParam[3] . '. we are getting it ready. ✨We\`ll let you know when it is shipped and on its way! 🚚 Thank you, Team National Draw';

                    $message = '🛍️Thanks you for Scheduling a pickup at the store Your 📦 Order ID ' . $request->templateBodyParam[0] . ' Pick up Date on ' . $request->templateBodyParam[1] . ', Here the  Secret Code ' . $request->templateBodyParam[2] . ' please share this secret code when picking up the Product. Total pen count ' . $request->templateBodyParam[3] . '.📍PICKUP AT THE STORE ADDRESS National Draw Gifts Trading LLC Warehouse #2, Plot #0284|0371 P.O Box #451394 Al Ttay, Al Khawaneej Dubai-UAE Landmark: Next to Tarahum Charity Foundation. Note: Kindly be advised that the collection window is between 11:00 AM-3:00 PM on (Mon-Fri) only.';


                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ],
                                "header" => [
                                    "type" => "IMAGE",
                                    "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/0b2165a2-0304-41d5-a33e-40168b2f7812.jpeg",
                                    "filename" => "0b2165a2-0304-41d5-a33e-40168b2f7812.jpeg"
                                ],
                                "buttons" => $request->buttons
                            ],

                            // [
                            //     [
                            //         "type" => "URL"
                            //     ]
                            // ]
                        ]
                    ];
                } else if ($request->templateName === 'out_of_delivery_tracking_v2') {

                    // $message = '🎉It\'s shipping day for your ordered product! We are happy to share that your Order ID ' . $request->templateBodyParam[0] . ' is Out for Delivery ⚡️📦. Our delivery executive will deliver the order at your doorstep today.';


                    // $message = '🎉It\'s shipping day for your ordered product! We are happy to share that your Order ID ' . $request->templateBodyParam[0] . ' is Out for Delivery ⚡️📦. Estimated Delivery by ' . $request->templateBodyParam[1] . '. Our delivery executive will deliver the order at your doorstep on or before the estimated Date.';

                    $message = '🎉It\'s shipping day for your ordered product! We are happy to share that your Order ID ' . $request->templateBodyParam[0] . ' is Out for Delivery ⚡️📦. Estimated Delivery by ' . $request->templateBodyParam[1] . '. Our delivery executive will deliver the order at your doorstep on or before the estimated Date. Tracking Details👇 ' . $request->templateBodyParam[2];

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ],
                                "buttons" => $request->buttons
                            ],
                            // "header" => [
                            //     "type" => "IMAGE",
                            //     "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/74ddff36-9882-4170-8f96-e0db8afac8ec.jpeg",
                            //     "filename" => "74ddff36-9882-4170-8f96-e0db8afac8ec.jpeg"
                            // ],

                        ]
                    ];
                } else if ($request->templateName === 'shipping_order_delivered') {

                    // $message = 'Hi ' . $request->templateBodyParam[0] . ' Thankyou for placing an order with National Draw!!! We have received your order on ' . $request->templateBodyParam[1] . ' and your Order ID ' . $request->templateBodyParam[2] . ', Total pen count ' . $request->templateBodyParam[3] . '. we are getting it ready. ✨We\`ll let you know when it is shipped and on its way! 🚚 Thank you, Team National Draw';

                    $message = 'Woohoo! ' . $request->templateBodyParam[0] . ', Order Delivered! 📦 Your National Draw Order ID ' . $request->templateBodyParam[1] . ' was delivered recently. 🛍️ 🌿Thank you for shopping at National Draw!📦 Your support means the world to us. ♻️ Let\'s keep moving forward together for a greener, more sustainable world! 🌍 #Sustainability #ThankYou 🙏 Love, Team National Draw';

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ],
                                "header" => [
                                    "type" => "IMAGE",
                                    "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/6664fbf7-7cf8-402d-8fe1-2e3ce844bea4.jpeg",
                                    "filename" => "6664fbf7-7cf8-402d-8fe1-2e3ce844bea4.jpeg"
                                ],
                                "buttons" => [
                                    [
                                        "type" => "URL"
                                    ]
                                ]
                            ],


                        ]
                    ];
                } else if ($request->templateName === 'winner_whatsapp_template_2g_v7_final_v2') {

                    // $message = '🎉It\'s shipping day for your ordered product! We are happy to share that your Order ID ' . $request->templateBodyParam[0] . ' is Out for Delivery ⚡️📦. Our delivery executive will deliver the order at your doorstep today.';


                    // $message = '🎉It\'s shipping day for your ordered product! We are happy to share that your Order ID ' . $request->templateBodyParam[0] . ' is Out for Delivery ⚡️📦. Estimated Delivery by ' . $request->templateBodyParam[1] . '. Our delivery executive will deliver the order at your doorstep on or before the estimated Date.';


                    $message = '🎊CONGRATULATIONS!!!!🎉🏆 Hello ' . $request->templateBodyParam[0] . ', You\'re our lucky winner of ' . $request->templateBodyParam[1] . ' Grams of gold in our "Daily Thrill Draw"-Day ' . $request->templateBodyParam[2] . ' 🌟✨ Keep the excitement going and stay tuned for more chances to win! 🎁🍀 📸 We would like to request you provide the photograph of you to add our winners gallery 👤 and publish our social media pages 🥳';


                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ]
                            ],
                            "header" => [
                                "type" => "IMAGE",
                                "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/2d9c21a1-5902-4778-ae30-3ba8229904e9.jpeg",
                                "filename" => "2d9c21a1-5902-4778-ae30-3ba8229904e9.jpeg"
                            ],
                            // "buttons" => [
                            //     [
                            //         "type" => "URL"
                            //     ]
                            // ]
                        ]
                    ];
                } else if ($request->templateName === 'rejected_withdrwal_template_v3') {

                    // $message = '🌿Thank you for shopping by our warehouse and picking up your product!📦  Your support means the world to us. ♻️ Let\'s keep moving forward together for a greener, more sustainable world! 🌍 #Sustainability#ThankYou🙏';

                    $message = '📪Thanks for submitted the Withdrawal request on ' . $request->templateBodyParam[0] . '!!! Hello ' . $request->templateBodyParam[1] . ', 📥We regret to inform you that your recent withdrawal request ' . $request->templateBodyParam[2] . ' has been rejected. Kindly find the reason for rejection here👇 ' . $request->templateBodyParam[3] . ' 🗳️Correct the above-rejected reason and submit again!!! 📮';

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ]
                            ],
                            // "header" => [
                            //     "type" => "IMAGE",
                            //     "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/74ddff36-9882-4170-8f96-e0db8afac8ec.jpeg",
                            //     "filename" => "74ddff36-9882-4170-8f96-e0db8afac8ec.jpeg"
                            // ],
                            "buttons" => [
                                [
                                    "type" => "URL"
                                ]
                            ]
                        ]
                    ];
                } else if ($request->templateName === 'ticket_purchase_template_final_v3') {

                    // $message = 'Hi ' . $request->templateBodyParam[0] . ' Thankyou for placing an order with National Draw!!! We have received your order on ' . $request->templateBodyParam[1] . ' and your Order ID ' . $request->templateBodyParam[2] . ', Total pen count ' . $request->templateBodyParam[3] . '. we are getting it ready. ✨We\`ll let you know when it is shipped and on its way! 🚚 Thank you, Team National Draw';


                    $message = 'Hi,' . $request->templateBodyParam[0] . ' 🛍️Thank you for your Purchase on ' . $request->templateBodyParam[1] . '! Your order has been confirmed Click below to view your ticket!👇 Draw valid for ' . $request->templateBodyParam[2] . ' from ' . $request->templateBodyParam[3] . ' to ' . $request->templateBodyParam[4] . '. Wish you a Best of Luck☘️!';

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ],
                                "header" => [
                                    "type" => "IMAGE",
                                    "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/d1c8a0a0-3329-4920-b54f-55b751934e4b.jpeg",
                                    "filename" => "d1c8a0a0-3329-4920-b54f-55b751934e4b.jpeg"
                                ],
                                "buttons" => $request->buttons
                            ],

                            // [
                            //     [
                            //         "type" => "URL"
                            //     ]
                            // ]
                        ]
                    ];
                } else if ($request->templateName === 'ticket_purchase_customer_template_v3') {

                    // $message = 'Hi ' . $request->templateBodyParam[0] . ' Thankyou for placing an order with National Draw!!! We have received your order on ' . $request->templateBodyParam[1] . ' and your Order ID ' . $request->templateBodyParam[2] . ', Total pen count ' . $request->templateBodyParam[3] . '. we are getting it ready. ✨We\`ll let you know when it is shipped and on its way! 🚚 Thank you, Team National Draw';


                    $message = 'Hi,' . $request->templateBodyParam[0] . ' 🛍️Thank you for your Purchase on ' . $request->templateBodyParam[1] . '! Your order has been confirmed Click below to view your ticket!👇 Draw valid for ' . $request->templateBodyParam[2] . ' from ' . $request->templateBodyParam[3] . ' to ' . $request->templateBodyParam[4] . '. Wish you a Best of Luck☘️!';

                    $ddRequest = [
                        'to' => $request->mobileNo,
                        'content' => [
                            'templateName' => $request->templateName,
                            'language' => $request->language,
                            'templateData' => [
                                'body' => [
                                    "placeholders" => $request->templateBodyParam
                                ],
                                "header" => [
                                    "type" => "IMAGE",
                                    "mediaUrl" => "https://quickscale-template-media.s3.ap-south-1.amazonaws.com/org_RVkRPWYu8Z/5aff19fd-7920-42c3-950b-362c716cce06.jpeg",
                                    "filename" => "5aff19fd-7920-42c3-950b-362c716cce06.jpeg"
                                ],
                                "buttons" => $request->buttons
                            ],

                            // [
                            //     [
                            //         "type" => "URL"
                            //     ]
                            // ]
                        ]
                    ];
                } else {
                    $response = ['status' => 'failed', 'message' => 'The Template not found.', 'error' => 'The Template not found.'];
                    goto returnFVI;
                }


                if (count($ddRequest) < 1) {
                    $response = ['status' => 'failed', 'message' => 'The Request build process failed.', 'error' => 'The Request build process failed.'];
                    goto returnFVI;
                }



                $ddRequest = ['messages' =>  [$ddRequest]];

                // dd(json_encode($ddRequest));

                $smsLog_arr = [
                    'isResend' => (isset($request->resendStatus) && $request->resendStatus === 'YES' ? $request->resendStatus : 'NO'),
                    'gateway' => '',
                    'mobile' => $request->mobileNo,
                    'details' => $message,
                    'ip' => $request->ip(),
                    'datetime' => date("Y-m-d H:i:s"),
                    'status' => '',
                    'site' => 'CUSTOMER',
                    'REQ_Time' => date("Y-m-d H:i:s"),
                    'RES_Time' => date("Y-m-d H:i:s"),
                    'smssendstatus' => '1',
                    'response' => '',
                    'token_response' => '',
                    'subject' => '',
                    'reference_id' => '',
                    'smsdetails' => '',
                    'smsstatus' => ''
                ];

                // dd($smsLog_arr);

                $insertData = DB::table('smslog')->insert($smsLog_arr);

                $smslogID = DB::getPdo()->lastInsertId();






                $headers = [
                    'Authorization' => env('ddAPIKey'),
                    'Content-Type' => 'application/json',
                    'accept' => 'application/json'
                ];

                $response = Http::withHeaders($headers)
                    ->post(env('ddAPIEndPoint'), $ddRequest);

                // $res = $response->body();


                $resultResponse = json_decode($response->body(), true);

                // dd(json_encode($ddRequest));

                $logUpdate = DB::table('smslog')->where('id', $smslogID)->update(['token_response' => json_encode($ddRequest), 'status' => json_encode($resultResponse), 'reference_id' => $resultResponse['messages'][0]['messageId'] ?? '', 'RES_Time' => date("Y-m-d H:i:s"),  'gateway' => 'doubleTick', 'smsstatus' => $resultResponse['messages'][0]['status'] ?? '']);

                if (isset($resultResponse['messages'][0]['status']) && $resultResponse['messages'][0]['status'] != '' && $resultResponse['messages'][0]['status'] === 'SENT') {
                    $response = ['status' => 'success', 'message' => 'WhatsApp Sent Successfully!', 'data' => ['gateWayResponse' => $resultResponse]];
                    goto returnFVI;
                } else {
                    $response = ['status' => 'failed', 'message' => 'Failed to send WhatsApp!', 'error' => $resultResponse];
                    goto returnFVI;
                }
                // dd($resultResponse['sid']);
            } else {
                $response = ['status' => 'failed', 'message' => 'Please use Valid Sender Name!', 'error' => 'Please use Valid Sender Name!'];
                goto returnFVI;
            }

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }



    public function dtCallBack(Request $request)
    {
        try {

            $response = [];

            $messageID = $request->messageId;
            $messageText = $request->message['templateMessage']['body']['text'];
            $to = $request->to;
            $status = $request->status;

            if ($messageID == '' || $messageID == null || $messageID == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please provide a valid Message ID', 'error' => 'Please provide a valid Message ID'];
                goto returnFVI;
            }

            if ($messageText == '' || $messageText == null || $messageText == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please provide a valid message text', 'error' => 'Please provide a valid message text'];
                goto returnFVI;
            }

            if ($to == '' || $to == null || $to == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please provide a valid customer no', 'error' => 'Please provide a valid customer no'];
                goto returnFVI;
            }

            $update = DB::table('smslog')
                ->where('reference_id', 'LIKE', $messageID)
                ->where('mobile', '=', $to)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->update([
                    'response' => json_encode($request->toArray()),
                    'smsstatus' => $status,
                    'details' => addslashes($messageText),
                    'SentDate' => Carbon::parse($request->statusTimestamp)->toDateTimeString()
                ]);

            if ($update) {
                $response = ['status' => 'success', 'message' => 'Message stored successfully!', 'data' => []];
                goto returnFVI;
            } else {
                $response = ['status' => 'failed', 'message' => 'Message storage process failed.', 'error' => 'Update Process failed'];
                goto returnFVI;
            }

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }


    public function bmsmscallback(Request $request)
    {
        try {

            $response = [];

            $messageID = $request->messageId;
            // $messageText = $request->message['templateMessage']['body']['text'];
            $to = $request->toNumber;
            $status = $request->status;

            if ($messageID == '' || $messageID == null || $messageID == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please provide a valid Message ID', 'error' => 'Please provide a valid Message ID'];
                goto returnFVI;
            }

            // if ($messageText == '' || $messageText == null || $messageText == 'null') {
            //     $response = ['status' => 'failed', 'message' => 'Please provide a valid message text', 'error' => 'Please provide a valid message text'];
            //     goto returnFVI;
            // }

            if ($to == '' || $to == null || $to == 'null') {
                $response = ['status' => 'failed', 'message' => 'Please provide a valid customer no', 'error' => 'Please provide a valid customer no'];
                goto returnFVI;
            }

            $update = DB::table('smslog')
                ->where('reference_id', 'LIKE', $messageID)
                ->where('mobile', '=', $to)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->update([
                    'response' => json_encode($request->toArray()),
                    'smsstatus' => str_replace("_", " ", $status),
                    'SentDate' => now()
                ]);

            if ($update) {
                $response = ['status' => 'success', 'message' => 'Message stored successfully!', 'data' => []];
                goto returnFVI;
            } else {
                $response = ['status' => 'failed', 'message' => 'Message storage process failed.', 'error' => 'Update Process failed'];
                goto returnFVI;
            }

            returnFVI:
            return response()->json($response);
        } catch (Exception $e) {

            $response = ['status' => 'failed', 'message' => 'Process failed', 'error' => ['message' => $e->getMessage(), 'code' => $e->getCode(), 'string' => $e->__toString()]];
            return response()->json($response);
        }
    }
}
