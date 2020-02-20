<?php
return [
    'tag_manager' => [
        'id' => !empty(env('GOOGLE_TAG_MANAGER_ID')) ? env('GOOGLE_TAG_MANAGER_ID') : null
    ]
];