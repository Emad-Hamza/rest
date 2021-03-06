<?php

namespace KnpU\CodeBattle\Controller\Api;

use KnpU\CodeBattle\Api\ApiProblem;
use KnpU\CodeBattle\Api\ApiProblemException;
use KnpU\CodeBattle\Controller\BaseController;
use KnpU\CodeBattle\Model\Programmer;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProgrammerController extends BaseController
{
    protected function addRoutes(ControllerCollection $controllers)
    {
        $controllers->post('/api/programmers', array($this, 'newAction'));

        $controllers->get('/api/programmers/{nickname}', array($this, 'showAction'))
            ->bind('api_programmers_show');

        $controllers->get('/api/programmers', array($this, 'listAction'));

        $controllers->put('/api/programmers/{nickname}', array($this, 'updateAction'));

        $controllers->delete('/api/programmers/{nickname}', array($this, 'deleteAction'));

        $controllers->patch('/api/programmers/{nickname}', array($this, 'updateAction'));
    }

    public function newAction(Request $request)
    {

        $programmer = new Programmer();
        $this->handleRequest($request, $programmer);
        $errors = $this->validate($programmer);

        if (!empty($errors)) {
            $this->throwApiProblemValidationException($errors);
        }

            $this->save($programmer);

        $data = $this->serializeProgrammer($programmer);
        $response = new JsonResponse($data, 201);


        $programmerUrl = $this->generateUrl(
            'api_programmers_show',
            ['nickname' => $programmer->nickname]
        );

        $response->headers->set('Location', $programmerUrl);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function showAction($nickname)
    {
        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);

        if (!$programmer) {
            $this->throw404('Crap! This programmer has deserted! We\'ll send a search party');
        }

        $data = $this->serializeProgrammer($programmer);

        $response = new Response(json_encode($data), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function listAction()
    {
        $programmers = $this->getProgrammerRepository()->findAll();

        $data = array('programmers' => array());
        foreach ($programmers as $programmer) {
            $data['programmers'][] = $this->serializeProgrammer($programmer);
        }

        $response = new Response(json_encode($data['programmers']), 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;


    }

    public function updateAction($nickname, Request $request)
    {
        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);

        if (!$programmer) {
            $this->throw404();
        }

        $this->handleRequest($request, $programmer);

        $errors = $this->validate($programmer);

        if (!empty($errors))
        {
            $this->throwApiProblemValidationException($errors);
        }

        $this->save($programmer);

        $data = $this->serializeProgrammer($programmer);
        $response = new JsonResponse($data, 200);


        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function handleRequest(Request $request, Programmer $programmer)
    {
        $data = json_decode($request->getContent(), true);

        $isNew = !$programmer->id;

        if ($data === null) {

            $problem = new ApiProblem(
                400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
            );


            throw new ApiProblemException($problem);
        }

        // determine which properties should be changeable on this request
        $apiProperties = array('avatarNumber', 'tagLine');

        if($isNew)
        {
            $apiProperties[] = 'nickname';
        }

        // update the properties
        foreach ($apiProperties as $property) {

            // if a property is missing on PATCH, that's ok - just skip it
            if (!isset($data[$property]) && $request->isMethod('PATCH')) {
                continue;
            }

            $val = isset($data[$property]) ? $data[$property] : null;
            $programmer->$property = $val;
        }
        $programmer->userId = $this->findUserByUsername('weaverryan')->id;
    }

    public function deleteAction($nickname)
    {
        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);

        if ($programmer) {
            $this->delete($programmer);
        }

        return new Response(null, '204');
    }


    private function serializeProgrammer(Programmer $programmer)
    {
        return array(
            'nickname' => $programmer->nickname,
            'avatarNumber' => $programmer->avatarNumber,
            'powerLevel' => $programmer->powerLevel,
            'tagLine' => $programmer->tagLine,
        );
    }

    private function throwApiProblemValidationException(array $errors)
    {

        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $apiProblem->set('errors', $errors);

        throw new ApiProblemException($apiProblem);
    }

}
