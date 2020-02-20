<?php

namespace App\Models\Repositories;

class RepositoryBase {
    /**
     * @var ResponseAbstract
     */
    protected $response = null;

    /**
     * @var ValidatorProxy
     */
    protected $validator = null;

    /**
     * @var EmployeeProfile
     */
    protected $profile = null;

    /**
     * Returns response message if any from the repo
     * @return Response
     */
    public function getResponse() {
        return $this->response;
    }
}
