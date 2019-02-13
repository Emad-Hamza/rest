<?php
/**
 * Created by PhpStorm.
 * User: Mahmoud S. Siddiq
 * Date: 13-Feb-19
 * Time: 1:55 PM
 */

namespace KnpU\CodeBattle\Api;


use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    private $apiProblem;


    public function __construct(ApiProblem $apiProblem, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $this->apiProblem = $apiProblem;

        parent::__construct(
            $apiProblem->getStatusCode(),
            $apiProblem->getTitle(),
            $previous,
            $headers,
            $code
        );
    }

    /**
     * @return ApiProblem
     */
    public function getApiProblem()
    {
        return $this->apiProblem;
    }





}