<?php

namespace Application\Models\Gateways\Job;

interface SearchGateway
{
    public function setParameters($parameters);
    public function performSearch();
}