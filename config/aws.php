<?php 
use Aws\Laravel\AwsServiceProvider;

return array(
	'credentials'=>[	
		    'key' => 'AKIAINBOC4DL2BCZVXOQ',
		    'secret' => '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'
			],    
	'region' => 'ap-southeast-2',
    'kmskey' => '',
    'version'=> 'latest',
    'signature' => 'v4',
    'buckets' => array(
        'default' => 'default',
        'careerone' => 'content.careeronecdn.com.au'
    ),
    'resumes' => array(
        'default' => 'default',
        'careerone' => 'resumes.careeronecdn.com.au'
    ),
    'stagingbuckets' => array(
        'default' => 'default',
        'careerone' => 'stagingcontent.careeronecdn.com.au'
    ),

// 		'region' => env('AWS_REGION', 'ap-southeast-2'),
// 		'version' => 'latest',
// 		'ua_append' => [
// 				'L5MOD/' . AwsServiceProvider::VERSION,
// 		],
		
);