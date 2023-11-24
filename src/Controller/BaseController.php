<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractController
{
    protected array $queryParams = []; 

    protected function loadQueryParameters(Request $request) {
        if ($request->getMethod() === Request::METHOD_GET || $request->getMethod() === Request::METHOD_DELETE ) {
            $this->queryParams = $request->query->all();
        }
    }

    protected function getPaginationParameters() : array {
        return [
            'page' => $this->queryParams['page'] ?? 1,
            'pageSize' => $this->queryParams['pageSize'] ?? 10,
            'sortName' => $this->queryParams['sortName'] ?? 0,
            'sortOrder' => $this->queryParams['sortOrder'] ?? 'asc',
            'returnUrl' => $this->queryParams['returnUrl'] ?? null,
        ];
    }

    protected function getAjax(): bool {
        if ( array_key_exists('ajax', $this->queryParams) ) {
            return $this->queryParams['ajax'] === 'true' ? true : false;
        }
        
        return false;
    }

    protected function render(string $view, array $parameters = [], Response $response = null): Response { 
        $paginationParameters = $this->getPaginationParameters();
        $viewParameters = array_merge($parameters, $paginationParameters);
        return parent::render($view, $viewParameters, $response);
    }

    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse {
        $paginationParameters = $this->getPaginationParameters();
        $viewParameters = array_merge($parameters, $paginationParameters);
        return parent::redirectToRoute($route, $viewParameters, $status);
    }    
}
