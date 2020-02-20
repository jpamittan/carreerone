<?php

return [	
			'jobstatus'=>[
					'0'=>  "" ,
					'100000000'=>  "New" ,
					'100000001'=>  "Passed" ,
					'100000002'=>  "Expired" ,
					'100000003'=>  "Placed" ,
					'100000004'=>  "Withdrawn" ,
					'100000005'=>  "Temporary - Full-time" ,
			],
			'jobmatchstatus'=>[
					'0'=>  "" ,
					'121660000'=>  "Match on salary, location & job category only" ,
					'121660001'=>  "Request RD" ,
					'121660002'=>  "RD requested – Awaiting response" ,
					'121660003'=>  "RD received – Assessing match" ,
					'121660004'=>  "RD received – Awaiting confirmation of match" ,
					'121660005'=>  "RD received – EiT has expressed interest" ,
					'121660006'=>  "RD received – EiT has deemed role unsuitable" ,
					'121660007'=>  "Not eligible to apply" ,
					'121660008'=>  "EiT not available for matching" ,
					'121660009'=>  "Role withdrawn by Agency" ,
					'121660010'=>  "Did not match – Capability and/or skills" ,
					'121660011'=>  "Did not match – Temporary/Part-time" ,
					'121660012'=>  "Did not match – Location" ,
					'121660018'=>  "Did not match – Salary" ,
					'121660020'=>  "Did not match – After review by Case Manager" ,
					'121660013'=>  "Did not match – After review with EiT" ,
					'121660014'=>  "Did not match – No response from EiT" ,
					'121660015'=>  "Matched for Assessment" ,
					'121660016'=>  "Matched for Assessment – Below grade" ,
					'121660017'=>  "Matched for Assessment – EiT withdrawn/declined" ,
					'121660019'=>  "On hold – Awaiting Agency" ,
 
			],

 

			'jobappliedprogressstatus'=>[
					'0'=>  "N/A" ,
					'121660000'=>  "Application due to be submitted" ,
					'121660001'=>  "Draft application submitted for review" ,
					'121660002'=>  "Draft reviewed by INS and sent to EiT" ,
					'121660003'=>  "Final application submitted by EiT" ,
					'121660004'=>  "Application submitted – Awaiting interview confirmation" ,
					'121660005'=>  "Screened on application" ,
					'121660006'=>  "Interview booked" ,
					'121660007'=>  "Assessment Centre booked" ,
					'121660008'=>  "Interviewed/Assessed – Awaiting outcome" ,
					'121660009'=>  "Role on hold" ,
					'121660010'=>  "Role withdrawn by Agency" ,
					'121660011'=>  "Offer – Trial based" ,
					'121660012'=>  "Offer – Direct appointment" ,
					'121660013'=>  "Declined/withdrawn – Accepted another role" ,
					 
					'121660014'=>  "Declined/withdrawn by EiT – Unsupported by INS" ,
					'121660015'=>  "Declined/withdrawn by EiT – Supported by INS" ,
				 
 
			],
			'donotdisplay_match'=>[ 
					'121660000' , 
				 	'121660001' ,
				  
			],

			'potential_match' => [ 
					'121660001' , 
				 	'121660002' ,
				 	'121660003' ,
				 	'121660004' ,
				 	'121660005' ,
				 	'121660006' ,
				 	'121660019' ,
				 	'121660021' ,
			],

			'potential_match_3pm' => [ 
					'121660001' , 
				 	'121660002' ,
				 	'121660003' ,
				 	'121660004' ,
				 	'121660019' ,
				 	'121660021' ,
			],//before 3pm on the deadline date

			'potential_match_1pm' => [ 
				 	'121660005' ,
				 	'121660006' ,
			],//before 1pm on the deadline date

			'match_history' => [
					'121660006' ,
					'121660007' ,
					'121660008' , 
				 	'121660009' ,
				 	'121660010' ,
				 	'121660011' ,
				 	'121660012' ,
				 	'121660013' ,
				 	'121660014' ,
				 	'121660018' ,
				 	'121660018' ,
				 	'121660020' ,
				 	'121660016' ,
				 	'121660017' ,
			],
			
			'application_monitor_match' => [
				 	'121660015' ,
			],

			'donotdisplay_applied' => [ 
					'121660005' , 
				 	'121660009' ,
				 	'121660010' ,
				 	'121660011' ,
				 	'121660012' ,
				 	'121660013' ,
				 	'121660014' ,
				 	'121660015' ,
				  
			],

			'application_monitor_applied' => [
				 	'121660000' ,
				 	'121660001' ,
				 	'121660002' ,
				 	'121660003' ,
				 	'121660004' ,
				 	'121660006' ,
				 	'121660007' ,
				 	'121660008' ,

			],

		    'job_match_statuses_expired' => [
			        '121660013',
			        '121660014',
			        '121660017',
			        '121660008',
		    ],

		    'potential_match_statuses_not_expired' => [
			        '121660021',
			        '121660005',
			        '121660006',
			        '121660019'
		    ]
/*
AS per email

    Role Match status are*
    "Options:

Does not Appear

121660000: Match on salary, location & job category only

121660001: Request RD

Appears in Potential Matches

121660002: RD requested – Awaiting response
121660003: RD received – Assessing match
121660004: RD received – Awaiting confirmation of match
121660005: RD received – EiT has expressed interest

Match History

121660006: RD received – EiT has deemed role unsuitable
121660019: On hold – Awaiting Agency
121660007: Not eligible to apply
121660008: EiT not available for matching
121660009: Role withdrawn by Agency
121660010: Did not match – Capability and/or skills
121660011: Did not match – Temporary/Part-time
121660012: Did not match – Location
121660018: Did not match – Salary
121660020: Did not match – After review by Case Manager
121660013: Did not match – After review with EiT
121660014: Did not match – No response from EiT

Application Monitor

121660015: Matched for Assessment

Match History

121660016: Matched for Assessment – Below grade
121660017: Matched for Assessment – EiT withdrawn/declined
Default: 121660000"

    Role applied status are*

"Options:

Application Monitor

121660000: Application due to be submitted
121660001: Draft application submitted for review
121660002: Draft reviewed by INS and sent to EiT
121660003: Final application submitted by EiT
121660004: Application submitted – Awaiting interview confirmation

Not Shown, Email to be sent to Case Manager to contact EIT by COB

121660005: Screened on application

Application Monitor

121660006: Interview booked
121660007: Assessment Centre booked
121660008: Interviewed/Assessed – Awaiting outcome

Not Shown

121660009: Role on hold
121660010: Role withdrawn by Agency
121660011: Offer – Trial based
121660012: Offer – Direct appointment
121660013: Declined/withdrawn – Accepted another role
121660014: Declined/withdrawn by EiT – Unsupported by INS
121660015: Declined/withdrawn by EiT – Supported by INS
Default: N/A"

*/
			 

			 
			
 
		
];

 