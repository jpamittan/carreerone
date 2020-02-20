<?php
namespace Application\Models\Proxies;

use Application\Models\Containers\ResponseAbstract;
use Application\Models\Validators\ValidatorSeed;
use Constants;
use Validator;

class ValidatorProxy {
    /**
     * Response object handler
     * @var Response
     */
    protected $response;

    /**
     * Validation Seed
     */
    protected $seed;

    /**
     * Constructor function
     */
    public function __construct(ValidatorSeed $seed, ResponseAbstract $response) {
        $this->seed = $seed;
        $this->response = $response;
    }

    /**
     * Function to validate based on passed in scope for the data
     * @param string $scope
     * @param mixed $data
     * @return Response $response
     */
    public function validate($scope, $attributes) {
        // get validation scope from the seed
        $validation_scope = $this->seed->getValidationScope($scope);
        $this->seed->loadCustomValidationRules();
        // get scope for validation errors
        $scope = $this->response->getScope();
        if (empty($validation_scope)) {
            $this->response->set($scope . '.validation.invalid_scope', Constants::RESP_CODE_VALIDATION_ERROR);
        } else {
            $validator = Validator::make($attributes, $validation_scope['rules'], $validation_scope['custom_messages']);
            // failed validation
            if ($validator->fails()) {
                $message_bag = $validator->messages();
                $this->response->set($scope . '.validation.failed', Constants::RESP_CODE_VALIDATION_ERROR, $message_bag->toArray());
            }
        }
        // set post data regardless of validator response
        $this->response->setData($attributes);
        return $this->response;
    }
}
