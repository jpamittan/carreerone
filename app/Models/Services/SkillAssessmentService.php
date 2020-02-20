<?php

namespace App\Models\Services;

use App\Models\Repositories\SkillAssessmentRepository;

/**
 * Class SkillAssesmentService
 * @package App\Models\Services
 */
class SkillAssessmentService {
    /**
     * @var SkillAssessmentRepository
     */
    protected $repository;

    /**
     * SkillAssessmentService constructor.
     * @param $repository
     */
    public function __construct(SkillAssessmentRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param $candidateId
     * @return mixed
     */
    public function getSkillsForCandidate($candidateId) {
        return $this->repository->find([
            'candidate_id' => $candidateId
        ])->get();
    }
}
