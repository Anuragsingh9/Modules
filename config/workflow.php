<?php

/*
return [
    'straight' => [
        'type'          => 'state_machine',
        'metadata' => ['validated_on' => 200],
        'marking_store' => [
            'type' => 'single_state',
        ],
        'supports'      => [\Modules\Newsletter\Entities\News::class],
        'places'        => [
            '',
            'pre_validate',
            'editorial_committee',
            'validated' => ['metadata' => 'this']
        ],
        'transitions'   => [
            'pre_validate' => ['from' => '', 'to' => 'pre_validate',],
            'editorial'    => ['from' => 'pre_validate', 'to' => 'editorial_committee'],
            'validate'     => ['from' => 'editorial_committee', 'to' => 'validated',]
        ],
    ]
];
*/
return [
    'news_status' => [
        'type'        => 'workflow', // or 'state_machine'
        'marking_store' => [
            'type' => 'single_state',
            'arguments' => ['status'],
        ],
        'metadata'    => [
            'title' => 'News Moderation Workflow',
        ],
        'supports'    => [\Modules\Newsletter\Entities\News::class],
        'places'      => [
            'pre_validated',
            'editorial_committee',
            'validated',
            'archived',
            'rejected',
            'deleted',
            'sent'
        ],
        'transitions' => [
            'to_editorial' => ['from' => 'pre_validated',
                'to'   => 'editorial_committee'],
            'reject'       => ['from' => 'rejected',
                'to'   => 'rejected'],
            'validate'     => ['from' => 'editorial_committee',
                'to'   => 'validated'],
            'archive'      => ['from' => 'rejected',
                'to'   => 'archived'],
            'send'         => ['from' => 'validated',
                'to'   => 'sent'],
        ],
    ]
];