<?php
namespace SbxCommon\Base;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\ModuleRouteListener;

abstract class AbstractController extends AbstractActionController
{
    protected function getCaption()
    {
        /**
         * @var \Zend\Mvc\Router\Http\RouteMatch $routeMatch
         */
        $routeParams = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch()->getParams();
        $moduleNamespace = substr($routeParams['__NAMESPACE__'], 0, strpos($routeParams['__NAMESPACE__'], '\\'));
        return strtolower($moduleNamespace . ':' . $routeParams['__CONTROLLER__'] . ':' . $routeParams['action']);
    }


    protected function getAfterActionRedirect($urlParams = array(), $routeName = null)
    {
        $routeMatch = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();

        if ($routeMatch !== null) {
            $routeMatchParams = $routeMatch->getParams();

            if (isset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER])) {
                $routeMatchParams['controller'] = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER];
                unset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER]);
            }

            if (isset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE])) {
                unset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE]);
            }
            $urlParams['action'] = 'index';
            $urlParams = array_merge($routeMatchParams, $urlParams);
        }
        if ($routeName == null) {
            $routeName = $routeMatch->getMatchedRouteName();
        }
        return $this->redirect()->toRoute($routeName, $urlParams, true);
    }
}