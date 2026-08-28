<?php

namespace App\Services;

use Carbon\Carbon;
use App\Http\Controllers\Api\v4\CarPoolJobController;

class FirebaseJobService
{
    protected string $projectId;
    // protected string $projectId2;
    protected string $accessToken;
    // protected string $accessToken2;
    protected string $fbCol;
    protected string $fbRMCol;

    public function __construct(string $projectId, string $accessToken)
    {
        $this->projectId   = $projectId;
        $this->accessToken = $accessToken;
        // $this->projectId2   = $projectId2;
        // $this->accessToken2 = $accessToken2;
        $this->fbCol = env('FIREBASE_COLLECTION');
        $this->fbRMCol = env('FIREBASE_RM_COLLECTION');
    }
    
    public function sendNotification(
        $token,
        $title,
        $body,
        $data = []
    ) {

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [

            'message' => [

                'token' => $token,

                'notification' => [
                    'title' => $title,
                    'body'  => $body
                ],

                'data' => $data,

                'android' => [
                    'priority' => 'high'
                ],

                'apns' => [
                    'headers' => [
                        'apns-priority' => '10'
                    ]
                ]
            ]
        ];

        $headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response, true);
    }

    public function updateConfirmStatus(string $jobNo, int $status): void
    {
        $fields = [
            'confirm_status' => ['integerValue' => (string) $status],
            'updated_at' => ['timestampValue' => now()->toIso8601String()]
        ];

        $this->patch(
            $fields, 
            ['confirm_status', 'updated_at'], // updateMask ensures we ONLY touch these 2 columns
            "{$this->fbCol}/{$jobNo}"
        );
    }
    
    private function firestoreUrl(string $path): string
    {
        return "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$path}";
    }

    private function request(string $method, string $url, array $payload = null)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => $payload ? json_encode($payload) : null
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    private function patch(array $fields, array $paths, string $docPath)
    {
        $mask = implode('&', array_map(
            fn($p) => "updateMask.fieldPaths={$p}",
            $paths
        ));

        $url = $this->firestoreUrl("{$docPath}?{$mask}");

        return $this->request('PATCH', $url, ['fields' => $fields]);
    }

    public function getOpenJobs(): array
    {
        $now = Carbon::now()->toIso8601String();

        $query = [
            'structuredQuery' => [
                'from' => [
                    ['collectionId' => $this->fbCol]
                ],
                'where' => [
                    'compositeFilter' => [
                        'op' => 'AND',
                        'filters' => [
                            [
                                'fieldFilter' => [
                                    'field' => ['fieldPath' => 'job_status'],
                                    'op' => 'IN',
                                    'value' => [
                                        'arrayValue' => [
                                            'values' => [
                                                ['stringValue' => 'created'],
                                                ['stringValue' => 'bidding']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            [
                                'fieldFilter' => [
                                    'field' => ['fieldPath' => 'pickup_date'],
                                    'op' => 'GREATER_THAN_OR_EQUAL',
                                    'value' => [
                                        'timestampValue' => $now
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'orderBy' => [
                    [
                        'field' => ['fieldPath' => 'pickup_date'],
                        'direction' => 'ASCENDING'
                    ]
                ],
                'limit' => 50
            ]
        ];

        $response = $this->request(
            'POST',
            "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery",
            $query
        );

        $documents = [];
        foreach ($response as $item) {
            if (isset($item['document'])) {
                $documents[] = $item['document'];
            }
        }

        return $documents;
    }
    
    public function getAdminJobs(): array
    {
        $today = Carbon::today()->toIso8601String();
    
        // Only fetch relevant marketplace jobs instead of entire collection
        $query = [
            'structuredQuery' => [
                'from' => [
                    ['collectionId' => $this->fbCol]
                ],
    
                // Firestore pre-filters BEFORE sending to Laravel
                'where' => [
                    'compositeFilter' => [
                        'op' => 'OR',
                        'filters' => [
    
                            // Active marketplace jobs
                            [
                                'fieldFilter' => [
                                    'field' => ['fieldPath' => 'job_status'],
                                    'op' => 'IN',
                                    'value' => [
                                        'arrayValue' => [
                                            'values' => [
                                                ['stringValue' => 'created'],
                                                ['stringValue' => 'bidding'],
                                                ['stringValue' => 'accept'],
                                                ['stringValue' => 'assigned']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
    
                            // Future pickup jobs
                            [
                                'fieldFilter' => [
                                    'field' => ['fieldPath' => 'pickup_date'],
                                    'op' => 'GREATER_THAN_OR_EQUAL',
                                    'value' => [
                                        'timestampValue' => $today
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
    
                'orderBy' => [
                    [
                        'field' => ['fieldPath' => 'pickup_date'],
                        'direction' => 'ASCENDING'
                    ]
                ],
    
                // Important: prevents reading thousands of jobs
                'limit' => 60
            ]
        ];
    
        $response = $this->request(
            'POST',
            "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery",
            $query
        );
    
        $jobs = [];
    
        foreach ($response as $item) {
    
            if (!isset($item['document']['fields'])) {
                continue;
            }
    
            $doc = $item['document'];
    
            $job = $this->normalize($doc['fields']);
    
            // Ensure job_no exists
            $job['job_no'] ??= basename($doc['name']);
    
            $jobs[] = $job;
        }
    
        return $jobs;
    }
    
    public function listJobs(array $options = [])
    {
        $limit = isset($options['limit'])
            ? min((int) $options['limit'], 30)
            : 30;
    
        $since = $options['since'] ?? null;
    
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
    
        $filters = [
            [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'job_status'],
                    'op' => 'IN',
                    'value' => [
                        'arrayValue' => [
                            'values' => [
                                ['stringValue' => 'created'],
                                ['stringValue' => 'bidding'],
                                ['stringValue' => 'accept'],
                                ['stringValue' => 'assigned']
                            ]
                        ]
                    ]
                ]
            ]
        ];
    
        // Optional created_at filter
        if ($since) {
            $filters[] = [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'pickup_date'],
                    'op'    => 'GREATER_THAN_OR_EQUAL',
                    'value' => [
                        'timestampValue' => $since
                    ]
                ]
                
            ];
        }
    
        $query = [
            'structuredQuery' => [
                'from' => [
                    ['collectionId' => $this->fbCol]
                ],
                'where' => [
                    'compositeFilter' => [
                        'op' => 'AND',
                        'filters' => $filters
                    ]
                ],
                'orderBy' => [
                    [
                        'field' => ['fieldPath' => 'pickup_date'],
                        'direction' => 'ASCENDING'
                    ]
                ],
                'limit' => $limit
            ]
        ];
    
        $ch = curl_init($url);
    
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS     => json_encode($query),
        ]);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        $data = json_decode($response, true);
    
        if (isset($data['error'])) {
            throw new \Exception(
                'Invalid Firestore response: ' . ($data['error']['message'] ?? 'Unknown error')
            );
        }
    
        $documents = [];
    
        foreach ($data as $row) {
            if (isset($row['document'])) {
                $documents[] = $row['document'];
            }
        }
    
        return $documents;
    }
    
    protected function normalize(array $fields): array
    {
        $data = [];

        foreach ($fields as $key => $value) {

            match (true) {

                isset($value['stringValue']) =>
                    $data[$key] = $value['stringValue'],

                isset($value['integerValue']) =>
                    $data[$key] = (int) $value['integerValue'],

                isset($value['doubleValue']) =>
                    $data[$key] = (float) $value['doubleValue'],

                isset($value['timestampValue']) =>
                    $data[$key] = date(
                        'Y-m-d H:i:s',
                        strtotime($value['timestampValue'])
                    ),

                isset($value['mapValue']['fields']) =>
                    $data[$key] = $this->normalize(
                        $value['mapValue']['fields']
                    ),

                isset($value['arrayValue']['values']) =>
                    $data[$key] = array_map(
                        fn ($v) => $this->normalize($v),
                        $value['arrayValue']['values']
                    ),

                default =>
                    $data[$key] = null
            };
        }

        return $data;
    }

    public function placeBidCopy(string $jobNo, int $userId, array $bidData)
    {
        $now = now()->toIso8601String();

        $fields = [
            'job_status' => ['stringValue' => 'bidding'],
            'bids_details' => [
                'mapValue' => [
                    'fields' => [
                        (string)$userId => [
                            'mapValue' => [
                                'fields' => [
                                    'amount' => ['doubleValue' => (float)$bidData['amount']],
                                    'show_amount' => ['doubleValue' => (float)$bidData['show_amount']],
                                    'remark' => ['stringValue' => (string)($bidData['remark'] ?? '')],
                                    'status' => ['stringValue' => 'inreview'],
                                    'b_name' => ['stringValue' => (string)($bidData['b_name'] ?? '')],
                                    'b_image' => ['stringValue' => (string)($bidData['b_image'] ?? '')],
                                    'b_mobile' => ['stringValue' => (string)($bidData['b_mobile'] ?? '')],
                                    'b_rating' => ['stringValue' => (string)($bidData['b_rating'] ?? '')],
                                    'kyc_id' => ['stringValue' => (string)($bidData['kyc_id'] ?? '')],
                                    'created_at' => ['timestampValue' => $now],
                                    'updated_at' => ['timestampValue' => $now],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->patch(
            $fields,
            ['bids_details','job_status'],
            "{$this->fbCol}/{$jobNo}"
        );
    }
    
    // public function placeBid(string $jobNo, int $userId, array $bidData)
    // {
    //     $now = now()->toIso8601String();
    
    //     $bidPath = "bids_details.$userId";
    
    //     $fields = [
    //         'job_status' => ['stringValue' => 'bidding'],
    
    //         $bidPath => [
    //             'mapValue' => [
    //                 'fields' => [
    //                     'amount' => ['doubleValue' => (float) $bidData['amount']],
    //                     'remark' => ['stringValue' => (string) ($bidData['remark'] ?? '')],
    //                     'status' => ['stringValue' => 'inreview'],
    //                     'b_name' => ['stringValue' => (string) ($bidData['b_name'] ?? '')],
    //                     'b_image' => ['stringValue' => (string) ($bidData['b_image'] ?? '')],
    //                     'b_mobile' => ['stringValue' => (string) ($bidData['b_mobile'] ?? '')],
    //                     'b_rating' => ['stringValue' => (string) ($bidData['b_rating'] ?? '')],
    //                     'kyc_id' => ['stringValue' => (string) ($bidData['kyc_id'] ?? '')],
    //                     'created_at' => ['timestampValue' => $now],
    //                     'updated_at' => ['timestampValue' => $now],
    //                 ]
    //             ]
    //         ]
    //     ];
    
    //     $this->patch(
    //         $fields,
    //         ['job_status', $bidPath], // VERY IMPORTANT
    //         "{$this->fbCol}/{$jobNo}"
    //     );
    // }
    
    public function placeBid(string $jobNo, int $userId, array $bidData)
    {
        $job = $this->getJob($jobNo);
        
        // return $job;
        if (!$job) {
            throw new \Exception('Job not found');
        }

        $bids = [];

        if (isset($job['bids_details']['mapValue']['fields'])) {
            $bids = $job['bids_details']['mapValue']['fields'];
        }

        if (
            isset($bids[$userId]['mapValue']['fields']['status']['stringValue']) &&
            in_array(
                $bids[$userId]['mapValue']['fields']['status']['stringValue'],
                ['accept', 'reject']
            )
        ) {
            throw new \Exception('Bid already accepted or rejected');
        }
        
        $existingBidFields = $bids[$userId]['mapValue']['fields'] ?? [];
        
        // return $existingBidFields;

        $bids[$userId] = [
            'mapValue' => [
                'fields' => [
                    'amount' => [
                        'doubleValue' => (float) $bidData['amount']
                    ],
                    
                    'show_amount' => [
                        'doubleValue' => (float) $bidData['show_amount']
                    ],
        
                    'remark' => [
                        'stringValue' => (isset($bidData['remark']) && trim($bidData['remark']) != '' && $bidData['remark'])
                            ? (string) $bidData['remark']
                            : ($existingBidFields['remark']['stringValue'] ?? '')
                    ],
                    
                    'b_name' => [
                        'stringValue' => (isset($bidData['b_name']) && trim($bidData['b_name']) != '' && $bidData['b_name'])
                            ? (string) $bidData['b_name']
                            : ($existingBidFields['b_name']['stringValue'] ?? '')
                    ],
                    
                    'b_image' => [
                        'stringValue' => (isset($bidData['b_image']) && trim($bidData['b_image']) != '' && $bidData['b_image'])
                            ? (string) $bidData['b_image']
                            : ($existingBidFields['b_image']['stringValue'] ?? '')
                    ],
                    
                    'b_rating' => [
                        'stringValue' => (isset($bidData['b_rating']) && trim($bidData['b_rating']) != '' && $bidData['b_rating'])
                            ? (string) $bidData['b_rating']
                            : ($existingBidFields['b_rating']['stringValue'] ?? '')
                    ],
                    'b_seater' => [
                        'stringValue' => (isset($bidData['b_seater']) && trim($bidData['b_seater']) != '' && $bidData['b_seater'])
                            ? (string) $bidData['b_seater']
                            : ($existingBidFields['b_seater']['stringValue'] ?? '')
                    ],
                    'b_cab' => [
                        'stringValue' => (isset($bidData['b_cab']) && trim($bidData['b_cab']) != '' && $bidData['b_cab'])
                            ? (string) $bidData['b_cab']
                            : ($existingBidFields['b_cab']['stringValue'] ?? '')
                    ],
                    'b_luggage' => [
                        'stringValue' => (isset($bidData['b_luggage']) && trim($bidData['b_luggage']) != '' && $bidData['b_luggage'])
                            ? (string) $bidData['b_luggage']
                            : ($existingBidFields['b_luggage']['stringValue'] ?? '')
                    ],
                    'b_language' => [
                        'stringValue' => (isset($bidData['b_language']) && trim($bidData['b_language']) != '' && $bidData['b_language'])
                            ? (string) $bidData['b_language']
                            : ($existingBidFields['b_language']['stringValue'] ?? '')
                    ],
                    'b_mobile' => [
                        'stringValue' => (isset($bidData['b_mobile']) && trim($bidData['b_mobile']) != '' && $bidData['b_mobile'])
                            ? (string) $bidData['b_mobile']
                            : ($existingBidFields['b_mobile']['stringValue'] ?? '')
                    ],
                    
                    'b_cab_no' => [
                        'stringValue' => (isset($bidData['b_cab_no']) && trim($bidData['b_cab_no']) != '' && $bidData['b_cab_no'])
                            ? (string) $bidData['b_cab_no']
                            : ($existingBidFields['b_cab_no']['stringValue'] ?? '')
                    ],
                    
                    
                    'kyc_id' => [
                        'stringValue' => (isset($bidData['kyc_id']) && trim($bidData['kyc_id']) != '' && $bidData['kyc_id'])
                            ? (string) $bidData['kyc_id']
                            : ($existingBidFields['kyc_id']['stringValue'] ?? '')
                    ],
        
                    'mock_type' => [
                        'stringValue' => (isset($bidData['mock_type']) && trim($bidData['mock_type']) != '' && $bidData['mock_type'])
                            ? (string) $bidData['mock_type']
                            : ($existingBidFields['mock_type']['stringValue'] ?? '')
                    ],
        
                    'status' => [
                        'stringValue' => 'inreview'
                    ],
        
                    'created_at' => [
                        'timestampValue' =>
                            $existingBidFields['created_at']['timestampValue']
                            ?? now()->toIso8601String()
                    ],
        
                    'updated_at' => [
                        'timestampValue' => now()->toIso8601String()
                    ],
                ]
            ]
        ];


        $payload = [
            'fields' => [
                'job_status' => ['stringValue' => 'bidding'],
                'bids_details' => [
                    'mapValue' => [
                        'fields' => $bids
                    ]
                ]
            ]
        ];

        $this->request(
            'PATCH',
            $this->firestoreUrl("{$this->fbCol}/{$jobNo}?updateMask.fieldPaths=bids_details&updateMask.fieldPaths=job_status"),
            $payload
        );
    }
    
    public function deleteJob(string $jobNo): void
    {
        /*
        |---------------------------------------------------------
        | Fetch ONLY bids_details field (partial document read)
        |---------------------------------------------------------
        */
        $res = $this->request(
            'GET',
            $this->firestoreUrl("{$this->fbCol}/{$jobNo}?mask.fieldPaths=bids_details")
        );
    
        if (!isset($res['fields']['bids_details']['mapValue']['fields'])) {
    
            $this->request(
                'DELETE',
                $this->firestoreUrl("{$this->fbCol}/{$jobNo}")
            );
    
            return;
        }
    
        $bids = [];
    
        foreach ($res['fields']['bids_details']['mapValue']['fields'] as $uid => $bid) {
    
            if (!isset($bid['mapValue']['fields'])) {
                continue;
            }
    
            $fields = $bid['mapValue']['fields'];
    
            $bids[$uid] = [
                'amount'    => $fields['amount']['doubleValue'] ?? 0,
                'remark'    => $fields['remark']['stringValue'] ?? '',
                'status'    => $fields['status']['stringValue'] ?? '',
                'mock_type' => $fields['mock_type']['stringValue'] ?? null,
            ];
        }
    
        // \DB::table('cus_job_temp')
        //     ->where('job_no', $jobNo)
        //     ->update([
        //         'bids_details' => json_encode($bids),
        //         'updated_at'   => now(),
        //     ]);
    
        /*
        |---------------------------------------------------------
        | Delete Firestore document
        |---------------------------------------------------------
        */
        $this->request(
            'DELETE',
            $this->firestoreUrl("{$this->fbCol}/{$jobNo}")
        );
    }
    
    public function deleteScheduleJob(string $jobNo): void
    {
        $this->request(
            'DELETE',
            $this->firestoreUrl("{$this->fbCol}/{$jobNo}")
        );
    }

    public function cancelBid(string $jobNo, int $userId)
    {
        $fieldPath = "bids_details.`{$userId}`";
    
        $url = $this->firestoreUrl(
            "{$this->fbCol}/{$jobNo}?updateMask.fieldPaths={$fieldPath}"
        );
    
        // Important: do NOT send bids_details in payload
        $payload = [
            'fields' => new \stdClass()
        ];
    
        $this->request('PATCH', $url, $payload);
    }
    
    public function rejectBid(string $jobNo, int $bidderId)
    {
        $fieldPath = "bids_details.`{$bidderId}`.status";
    
        $url = $this->firestoreUrl(
            "{$this->fbCol}/{$jobNo}?updateMask.fieldPaths={$fieldPath}"
        );
    
        $payload = [
            'fields' => [
                'bids_details' => [
                    'mapValue' => [
                        'fields' => [
                            (string)$bidderId => [
                                'mapValue' => [
                                    'fields' => [
                                        'status' => [
                                            'stringValue' => 'reject'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    
        $this->request('PATCH', $url, $payload);
    }

    // public function updateBidStatus(string $jobNo, int $bidderId, string $status): void
    // {
    //     $fields = [
    //         'bids_details' => [
    //             'mapValue' => [
    //                 'fields' => [
    //                     (string)$bidderId => [
    //                         'mapValue' => [
    //                             'fields' => [
    //                                 'status' => ['stringValue' => $status],
    //                                 'updated_at' => ['timestampValue' => now()->toIso8601String()]
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ]
    //     ];

    //     $this->patch($fields, ['bids_details'], "{$this->fbCol}/{$jobNo}");
    // }
    
    public function updateBidStatus(string $jobNo, int $bidderId, string $status): void
    {
        $job = $this->getJob($jobNo);
    
        if (
            !$job ||
            !isset($job['bids_details']['mapValue']['fields'][$bidderId])
        ) {
            throw new \Exception('Bid not found in Firebase.');
        }
    
        /*
        |--------------------------------------------------------------------------
        | Update Selected Bid Status
        |--------------------------------------------------------------------------
        */
        $job['bids_details']['mapValue']['fields'][$bidderId]
            ['mapValue']['fields']['status'] = [
                'stringValue' => $status
            ];
    
        /*
        |--------------------------------------------------------------------------
        | Decide Job Status
        |--------------------------------------------------------------------------
        */
        $currentJobStatus = $job['job_status']['stringValue'] ?? 'pending';
    
        switch ($status) {
            case 'accept':
            case 'accepted':
                $jst = 'accept';
                break;
    
            case 'cancelled':
                $jst = 'cancelled';
                break;
    
            case 'reject':
            case 'rejected':
                $jst = $currentJobStatus; // keep existing job status
                break;
    
            default:
                $jst = $currentJobStatus; // fallback safety
                break;
        }
    
        /*
        |--------------------------------------------------------------------------
        | Payload
        |--------------------------------------------------------------------------
        */
        $payload = [
            'fields' => [
                'bids_details' => [
                    'mapValue' => [
                        'fields' => $job['bids_details']['mapValue']['fields']
                    ]
                ],
                'job_status' => [
                    'stringValue' => $jst
                ]
            ]
        ];
    
        /*
        |--------------------------------------------------------------------------
        | Send PATCH Request
        |--------------------------------------------------------------------------
        */
        $this->request(
            'PATCH',
            $this->firestoreUrl(
                "{$this->fbCol}/{$jobNo}"
                . "?updateMask.fieldPaths=bids_details"
                . "&updateMask.fieldPaths=job_status"
            ),
            $payload
        );
    }
    
    private function parseFirestoreValue($value)
    {
        if (isset($value['stringValue'])) {
            return $value['stringValue'];
        }
    
        if (isset($value['integerValue'])) {
            return (int) $value['integerValue'];
        }
    
        if (isset($value['doubleValue'])) {
            return (float) $value['doubleValue'];
        }
    
        if (isset($value['booleanValue'])) {
            return (bool) $value['booleanValue'];
        }
    
        if (isset($value['mapValue'])) {
            $result = [];
            $fields = $value['mapValue']['fields'] ?? [];
    
            foreach ($fields as $key => $val) {
                $result[$key] = $this->parseFirestoreValue($val);
            }
    
            return $result;
        }
    
        if (isset($value['arrayValue'])) {
            $result = [];
            $values = $value['arrayValue']['values'] ?? [];
    
            foreach ($values as $val) {
                $result[] = $this->parseFirestoreValue($val);
            }
    
            return $result;
        }
    
        return null;
    }

    public function getMyJobs(array $jobNos): array
    {
        if (empty($jobNos)) return [];
    
        $documents = array_map(fn($j) =>
            "projects/{$this->projectId}/databases/(default)/documents/{$this->fbCol}/{$j}",
            $jobNos
        );
    
        $response = $this->request(
            'POST',
            "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:batchGet",
            ['documents' => $documents]
        );
    
        $results = [];
    
        foreach ($response as $doc) {
            if (!isset($doc['found']['fields'])) continue;
    
            $name   = basename($doc['found']['name']);
            $fields = $doc['found']['fields'];
    
            // ✅ FIXED: Properly parse bids_details
            if (isset($fields['bids_details'])) {
                $results[$name] = $this->parseFirestoreValue($fields['bids_details']);
            } else {
                $results[$name] = [];
            }
        }
    
        return $results;
    }

    public function updateJobStatus(string $jobNo, string $status): void
    {
        $fields = [
            'job_status' => ['stringValue' => $status],
            'updated_at' => ['timestampValue' => now()->toIso8601String()]
        ];

        $this->patch($fields, ['job_status','updated_at'], "{$this->fbCol}/{$jobNo}");
    }
    
    public function getJob(string $jobNo): ?array
    {
        $res = $this->request(
            'GET',
            $this->firestoreUrl("{$this->fbCol}/{$jobNo}")
        );
    
        if (isset($res['error'])) {
            \Log::error('Firestore getJob failed', [
                'jobNo' => $jobNo,
                'error' => $res['error']
            ]);
    
            if (($res['error']['code'] ?? null) === 404) {
                return null;
            }
    
            throw new \Exception(
                'Firebase getJob failed: ' . ($res['error']['message'] ?? 'Unknown error')
            );
        }
    
        return $res['fields'] ?? null;
    }
    
    public function getJobBidders(string $jobNo): array
    {
        $job = $this->getJob($jobNo);
    
        if (
            !$job ||
            !isset($job['bids_details']['mapValue']['fields'])
        ) {
            return [];
        }
    
        return $job['bids_details']['mapValue']['fields'];
    }
    
    public function updateUserDetailsKeys(string $jobNo, array $userDetails): void
    {
        if (empty($userDetails)) {
            return;
        }
    
        $fields = [];
        $updateMask = [];
    
        foreach ($userDetails as $key => $value) {
    
            // Path: user_details.key
            $path = "user_details.{$key}";
            $updateMask[] = "updateMask.fieldPaths={$path}";
    
            if (is_int($value)) {
                $fields['user_details']['mapValue']['fields'][$key] = [
                    'integerValue' => (string) $value
                ];
            } elseif (is_numeric($value)) {
                $fields['user_details']['mapValue']['fields'][$key] = [
                    'doubleValue' => (float) $value
                ];
            } else {
                $fields['user_details']['mapValue']['fields'][$key] = [
                    'stringValue' => (string) $value
                ];
            }
        }
    
        // Updated timestamp
        $fields['updated_at'] = [
            'timestampValue' => now()->toIso8601String()
        ];
        $updateMask[] = 'updateMask.fieldPaths=updated_at';
    
        $payload = ['fields' => $fields];
    
        $url = $this->firestoreUrl(
            "{$this->fbCol}/{$jobNo}?" . implode('&', $updateMask)
        );
    
        $response = $this->request('PATCH', $url, $payload);
    
        if (isset($response['error'])) {
            throw new \Exception(
                'Firebase updateUserDetailsKeys failed: ' .
                ($response['error']['message'] ?? 'Unknown error')
            );
        }
    }
    
    public function editJob(string $jobNo, array $data): void
    {
        if (empty($data)) {
            throw new \Exception('No data provided for job update');
        }
    
        $fields = [];
        $updateMask = [];
    
        foreach ($data as $key => $value) {
    
            // Skip null values (do not overwrite unintentionally)
            if ($value === null) {
                continue;
            }
    
            $updateMask[] = "updateMask.fieldPaths={$key}";
    
            // Detect Firestore value type
            if (is_int($value)) {
                $fields[$key] = [
                    'integerValue' => (string) $value
                ];
            } elseif (is_float($value) || is_numeric($value)) {
                $fields[$key] = [
                    'doubleValue' => (float) $value
                ];
            } elseif ($this->isIsoDate($value)) {
                $fields[$key] = [
                    'timestampValue' => $value
                ];
            } else {
                $fields[$key] = [
                    'stringValue' => (string) $value
                ];
            }
        }
    
        // Always update timestamp
        $fields['updated_at'] = [
            'timestampValue' => now()->toIso8601String()
        ];
        $updateMask[] = 'updateMask.fieldPaths=updated_at';
    
        $payload = [
            'fields' => $fields
        ];
    
        $url = $this->firestoreUrl(
            "{$this->fbCol}/{$jobNo}?" . implode('&', $updateMask)
        );
    
        $response = $this->request('PATCH', $url, $payload);
    
        if (isset($response['error'])) {
            throw new \Exception(
                'Firebase editJob failed: ' .
                ($response['error']['message'] ?? 'Unknown error')
            );
        }
    }
    
    public function assignDriver(string $jobNo, int $driverId, int $ownerId): void
    {
        $payload = [
            'fields' => [
                'assigned_to' => [
                    'integerValue' => (string) $driverId
                ],
                'assigned_by' => [
                    'integerValue' => (string) $ownerId
                ],
                'job_status' => [
                    'stringValue' => 'assigned'
                ],
                'assigned_at' => [
                    'timestampValue' => now()->toIso8601String()
                ]
            ]
        ];
    
        $this->request(
            'PATCH',
            $this->firestoreUrl(
                "{$this->fbCol}/{$jobNo}?updateMask.fieldPaths=assigned_to" .
                "&updateMask.fieldPaths=assigned_by" .
                "&updateMask.fieldPaths=job_status" .
                "&updateMask.fieldPaths=assigned_at"
            ),
            $payload
        );
    }
    
    
    public function updateScheduleStatus(string $jobNo, array $statusData): void
    {
        try {
            $now = now()->toIso8601String();
            $schStatusFields = [];
    
            foreach ($statusData as $date => $drivers) {
                $driverFields = [];
    
                foreach ($drivers as $driverId => $data) {
                    $amount = $data['amount'] ?? null;
    
                    // Ensure the driverId is treated as a string key for the map
                    $driverFields[(string)$driverId] = [
                        'mapValue' => [
                            'fields' => [
                                'status' => ['stringValue' => (string)($data['status'] ?? '')],
                                'sch_id' => ['integerValue' => (string)($data['sch_id'] ?? 0)],
                                'amount' => $amount !== null 
                                    ? ['integerValue' => (string)$amount] 
                                    : ['stringValue' => ''],
                                'updated_at' => [
                                    'timestampValue' => $data['updated_at'] ?? $now
                                ]
                            ]
                        ]
                    ];
                }
    
                $schStatusFields[(string)$date] = [
                    'mapValue' => [
                        'fields' => $driverFields
                    ]
                ];
            }
    
            $payload = [
                'fields' => [
                    'sch_status' => [
                        'mapValue' => [
                            'fields' => $schStatusFields
                        ]
                    ]
                ]
            ];
    
            $this->request(
                'PATCH',
                $this->firestoreUrl("{$this->fbCol}/{$jobNo}?updateMask.fieldPaths=sch_status"),
                $payload
            );
    
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Specifically catch Laravel HTTP client errors
            \Log::error("Firestore Update Failed: " . $e->getMessage(), [
                'jobNo' => $jobNo,
                'response' => $e->response->json() ?? 'No response body'
            ]);
            throw $e; // Re-throw if you want the caller to handle it
    
        } catch (\Exception $e) {
            // Catch general PHP errors (like array key issues or connectivity)
            \Log::error("General Error in updateScheduleStatus: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function createFirebaseJob($jobNo, $jobData)
    {
        $controller = new CarPoolJobController();

        return $controller->createFirebaseJob(
            $jobNo,
            $jobData
        );
    }
    
    public function sendTopicNotification(
        string $topic,
        string $title,
        string $body,
        array $data = []
    )
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    
        $payload = [
            'message' => [
    
                'topic' => $topic,
    
                'notification' => [
                    'title' => $title,
                    'body'  => $body
                ],
    
                'data' => $data,
    
                'android' => [
                    'priority' => 'high'
                ],
    
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10'
                    ]
                ]
            ]
        ];
    
        $headers = [
            'Authorization: Bearer '.$this->accessToken,
            'Content-Type: application/json'
        ];
    
        $ch = curl_init();
    
        curl_setopt_array($ch,[
            CURLOPT_URL=>$url,
            CURLOPT_POST=>true,
            CURLOPT_HTTPHEADER=>$headers,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_POSTFIELDS=>json_encode($payload)
        ]);
    
        $response=curl_exec($ch);
    
        curl_close($ch);
    
        return json_decode($response,true);
    }
    
    public function subscribeToTopic(string $deviceToken, string $topicName)
    {
        // 1. Get the OAuth2 Access Token using your existing method
        $accessToken = $this->accessToken;

        if (!$accessToken) {
            return [
                'status'  => 'error',
                'message' => 'Failed to generate OAuth access token.'
            ];
        }

        $url = 'https://iid.googleapis.com/iid/v1:batchAdd';

        $payload = [
            'to' => '/topics/' . $topicName,
            'registration_tokens' => [$deviceToken]
        ];

        // 4. Execute the cURL request with Authorization headers
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            // Google's IID API requires the project ID to be passed if using specific scopes
            'access_token_auth: true' 
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        // 5. Handle success or failure responses
        if ($httpCode === 200 && isset($responseData['results'][0])) {
            // Check if there was an error specific to this token inside the batch
            if (isset($responseData['results'][0]['error'])) {
                return [
                    'status' => 'error',
                    'message' => 'Subscription failed: ' . $responseData['results'][0]['error']
                ];
            }

            return [
                'status' => 'success',
                'message' => "Successfully subscribed token to topic: {$topicName}"
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Firebase API returned an error.',
            'details' => $responseData
        ];
    }
    
    public function manageTopicSubscription(string $action, string $deviceToken, string $topicName)
    {
        
        if ($action === 'subscribe') {
            $url = 'https://iid.googleapis.com/iid/v1:batchAdd';
        } elseif ($action === 'unsubscribe') {
            $url = 'https://iid.googleapis.com/iid/v1:batchRemove';
        } else {
            return [
                'status'  => 'error',
                'message' => 'Invalid action key. Use "subscribe" or "unsubscribe".'
            ];
        }

        $accessToken = $this->accessToken;
        if (!$accessToken) {
            return [
                'status'  => 'error',
                'message' => 'Failed to generate OAuth access token.'
            ];
        }

        // 3. Setup payload requirements
        $payload = [
            'to' => '/topics/' . $topicName,
            'registration_tokens' => [$deviceToken]
        ];

        // 4. Execute the cURL request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'access_token_auth: true' 
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $responseData = json_decode($response, true);

        // 5. Check if the server execution was successful
        if ($httpCode === 200 && isset($responseData['results'][0])) {
            if (isset($responseData['results'][0]['error'])) {
                return [
                    'status' => 'error',
                    'message' => "Action '{$action}' failed for token: " . $responseData['results'][0]['error']
                ];
            }

            return [
                'status' => 'success',
                'message' => "Successfully executed '{$action}' for topic: {$topicName}"
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Firebase API returned an error response.',
            'details' => $responseData
        ];
    }
    
    public function sendTokenNotification(
        $token,
        $title,
        $body,
        array $data = []
    )
    {
    
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    
        $payload = [
    
            'message' => [
    
                'token' => $token,
    
                'notification' => [
    
                    'title' => $title,
    
                    'body' => $body
    
                ],
    
                'data' => $data
    
            ]
    
        ];
    
        $client = new \GuzzleHttp\Client();
    
        $response = $client->post($url, [
    
            'headers' => [
    
                'Authorization' => 'Bearer ' . $this->accessToken,
    
                'Content-Type' => 'application/json'
    
            ],
    
            'json' => $payload
    
        ]);
    
        return json_decode($response->getBody(), true);
    
    }
    
}