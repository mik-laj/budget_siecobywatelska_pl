<?php
/**
 * Created by PhpStorm.
 * User: andrzej
 * Date: 20.09.16
 * Time: 22:37
 */

namespace Sowp\BudgetBundle\Controller;


use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RootApiController extends FOSRestController
{
    /**
     * @Rest\View()
     * @Rest\Get(path="/")
     */
    public function rootAction(Request $request) {
        /** @var Router $router */
        $router = $this->get('router');

        return array(
            "_links"=> array(
                "category"=> array("href"=> $router->generate('sowp_budget_category_api_get_categories', array(), UrlGeneratorInterface::ABSOLUTE_URL)),
                "contracts"=> array("href"=> $router->generate('sowp_budget_contract_api_get_contracts', array(), UrlGeneratorInterface::ABSOLUTE_URL)),
            )
        );
    }
}