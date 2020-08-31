<?php

return [
         'validations'=>[
             'news'=>[ // TODO-Please set the max value for input
                 'title'=>'30',
                 'header'=>'30',
                 'description'=>'50',
             ],
         ],
        's3'=>[
            'news_image'=>'public/Images', // TODO-Please provide the path of s3
        ],
];
