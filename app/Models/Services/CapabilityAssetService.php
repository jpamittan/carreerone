<?php

namespace App\Models\Services;

use App\Libraries\Sanitize;
use App\Models\Containers\DataObject;
use Format;
use App\Models\Repositories\RepositoryBase;
use DB;
use App\Models\Entities\User;
use App\Models\Entities\CapabilityMatch;
use App\Models\Entities\CandidateSkillMatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Config, Redirect, Session, URL, Validator, View;

class CapabilityAssetService {

    /**
     * Response object with details
     */
    private $response = null;

    /**
     * Response object with details
     */
    private $repo = null;

    /**
     * ALl class dependencies in the constructor
     */
    public function __construct() {
        $this->repo = app()->make('App\Models\Repositories\CapabilityMatchRepository');
    }

    public function matchCapability() {
        $start = microtime(true);
        $candidate_ids = $this->repo->getCandidates();
        $job_ids = $this->repo->getJobIDs();
        $job_capability = [];
        $core_status = '';
        $matches = [];
        foreach ($job_ids as $job_id) {
            $job_capabs = $this->repo->getJobCapabilities($job_id);

            $job_capability[$job_id->job_id] = [];
            foreach ($job_capabs as $job_capab => $value1) {
                $job_capability[$job_id->job_id][$value1->capability_name_id] = [
                    'level_id' => $value1->level_id,
                    $value1->core_status,
                    'core_status' => $value1->core_status
                ];
            }
        }
        foreach ($candidate_ids as $candidate_id) {
            $cand_capabs = $this->repo->getCandidateCapabilities($candidate_id);
            $now = date('Y-m-d H:i:s');

            try {
                foreach ($job_ids as $job_id) {
                    // Empty all the previous matches for the give Job and Candidate ID.
                    DB::table('ins_capability')->where('job_id', '=', $job_id->job_id)->where('candidate_id', '=', $candidate_id->candidate_id)->delete();
                    // The total number of capabilities for the given JOB
                    $totalNumberOfCapabilities = count($job_capability[$job_id->job_id]);
                    if ($totalNumberOfCapabilities > 0) {
                        // Count the core capabilities and other capabilities
                        $coreCapabilitiesCount = count(collect($job_capability[$job_id->job_id])->where('core_status', 1));
                        // Count the number of other capabilities.
                        $otherCapabilitiesCount = $totalNumberOfCapabilities - $coreCapabilitiesCount;
                        // Calculate the score for a Core Capability based on the total number of capabilities.
                        // The sum of all the core capabilities should be 80% of the total available score.
                        $coreCapabilityScore = 8 / (($coreCapabilitiesCount > 0) ? $coreCapabilitiesCount : 1);

                        // Calculate the score for a Other Capability based on the total number of capabilities.
                        // The sum of all the other capabilities should be 20% of the total available score.
                        $otherCapabilityScore = 2 / (($otherCapabilitiesCount > 0) ? $otherCapabilitiesCount : 1);
                        // If we don't have any core capabilities. Distribute total score to other capabilities.
                        if ($coreCapabilitiesCount == 0) {
                            $otherCapabilityScore = 10 / $otherCapabilitiesCount;
                        }
                        // If we don't have any other capabilities. Distribute total score to core capabilities.
                        if ($otherCapabilitiesCount == 0) {
                            $coreCapabilityScore = 10 / $coreCapabilitiesCount;
                        }
                        foreach ($cand_capabs as $cand_capab => $candidate) {
                            // We only score the criteria which are met: 121660000: MET 121660003 PARTIALLY MET
                            if (!empty($candidate->criteria) && in_array($candidate->criteria, ['121660000', '121660003'])) {
                                // Set the default values first.
                                $score = 0;
                                $level_id = 0;
                                $core_status = 0;
                                // Load the Capability from Job's capabilities using the ID in the candidates capabilities.
                                if (isset($job_capability[$job_id->job_id][$candidate->capability_name_id])) {

                                    $core_status = $job_capability[$job_id->job_id][$candidate->capability_name_id]['core_status'];
                                    $level_id = $job_capability[$job_id->job_id][$candidate->capability_name_id]['level_id'];
                                    // If $candidate capability matches or is greater than the job.
                                    if ($level_id <= $candidate->level_id) {
                                        $score = ($core_status == 1) ? $coreCapabilityScore : $otherCapabilityScore;

                                        // If the criteria is partially met the score is 50%
                                        if ($candidate->criteria == '121660003') {
                                            $score = $score * 0.5;
                                        }
                                    }
                                }
                                //Delete means this will be empty??
                                $capability_id = $candidate->capability_name_id;
                                // Create a record into the database.
                                $matches[] = [
                                    'job_id' => $job_id->job_id,
                                    'candidate_id' => $candidate_id->candidate_id,
                                    'capability_name_id' => $capability_id,
                                    'level_id' => $level_id,
                                    'core_status' => $core_status,
                                    'score' => $score,
                                    'created_at' => $now,
                                    'updated_at' => $now
                                ];
                            }
                        }
                    }
                }
                if (!empty($matches)) {
                    CapabilityMatch::insert($matches);
                    $matches = [];
                }
            } catch (\Exception $e) {
                logger($e->getMessage());
                logger($e->getTraceAsString());
            }
        }
    }

    public function capabPercentage($capability_id, $job_id, $candidate_id) {
        $core = $this->repo->getCpabaMatchID($capability_id, $job_id, $candidate_id);
        if ($core->core_status == 1 && $core->score == 0.5) {
            $percent = $this->calculateCapabilityPercentage1($job_id);
            $this->repo->UpdateCapabpercentage($core->id, $percent);
        } else if ($core->core_status == 0 && $core->score == 0.5) {
            $percent = $this->calculateCapabilityPercentage0($job_id);
            $this->repo->UpdateCapabpercentage($core->id, $percent);
        }
    }

    public function calculateCapabilityPercentage1($job_id) {
        $core1 = $this->repo->getCore1($job_id);
        $capabilitypercent = ((1 / $core1) * 0.8) * 100;
        return $capabilitypercent;
    }

    public function calculateCapabilityPercentage0($job_id) {
        $core1 = $this->repo->getCore0($job_id);
        $capabilitypercent = ((1 / $core1) * 0.2) * 100;
        return $capabilitypercent;
    }
}
