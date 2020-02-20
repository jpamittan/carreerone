<?php

namespace App\Models\Repositories;

use App\Models\Entities\SkillAssesment;

/**
 * Class SkillAssessmentRepository
 * @package App\Models\Repositories
 */
class SkillAssessmentRepository extends RepositoryBase {
    /**
     * @param $id
     * @return mixed
     */
    public function get($id) {
        return SkillAssesment::find($id);
    }

    /**
     * @param array $where
     * @param int $limit
     * @param int $offset
     *
     * @return mixed
     */
    public function find(array $where, $limit = 1000, $offset = 0) {
        return SkillAssesment::where($where)->limit($limit)->offset($offset)->get();
    }
}
