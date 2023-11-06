<?php

use App\Constants\GeneralConst;

return [
    'ADMIN_APP_NAME'                        => 'Estimated Management',
    'FRONT_APP_NAME'                        => 'Estimated Management System',
    'PROJECT_MANAGEMENT'                    => 'Project Management',
    'INQUIRY_MANAGEMENT'                    => 'Inquiry Management',
    'CUSTOMER_MANAGEMENT'                   => 'Customer Management',
    'ACCOUNT_MANAGEMENT'                    => 'Account Management',
    'WIKI'                                  => 'Wiki',

    'customer_status'   => [
        GeneralConst::PRIVATE   => 'Private',
        GeneralConst::PUBLIC    => 'Public'
    ],

    'inquiry_status'                        => [
        GeneralConst::NOT_STARTED           => 'Not Started',
        GeneralConst::ANSWERED              => 'Answered',
        GeneralConst::ADDITIONAL_QUESTION   => 'Additional Question',
        GeneralConst::CLOSE                 => 'Close',
        GeneralConst::NOT_REQUIRED          => 'Not Required'
    ],

    'priority'                              => [
        GeneralConst::LOW                   => 'Low',
        GeneralConst::MEDIUM                => 'Medium',
        GeneralConst::HIGH                  => 'High'
    ],

    'role_list' =>  [
        GeneralConst::SALES                 => 'Sales',
        GeneralConst::MTM                   => 'MTM'
    ],

    'project_status' => [
        GeneralConst::NOT_STARTED           => 'Not Started',
        GeneralConst::IN_PROGRESS           => 'In Progress',
        GeneralConst::COMPLETED             => 'Completed',
        GeneralConst::CONFIRMING            => 'Confirming',
        GeneralConst::REPORT_TO_CUSTOMER    => 'Report To Customer',
        GeneralConst::NO_RESPONSE_REQUIRED  => 'No Response Required'
    ],

    'project_type'=> [
        GeneralConst::WEB                   => 'WEB',
        GeneralConst::SYSTEM                => 'System',
        GeneralConst::WEB_AND_SYSTEM        => 'Web & System'
    ]
];
