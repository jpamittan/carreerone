<?php
namespace App\Models\Gateways;

interface HttpGateway
{
  /**
   * Function that will get content from url
   * @param string $url
   * @param integer $timeout
   * @return mixed
   */
  public function get($url, $timeout);
}
