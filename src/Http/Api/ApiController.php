<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\SharpProtectedController;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Entities\SharpEntityManager;

abstract class ApiController extends SharpProtectedController
{
    protected SharpEntityManager $entityManager;
    
    public function __construct()
    {
        parent::__construct();
        $this->entityManager = app(SharpEntityManager::class);
    }

    protected function getListInstance(string $entityKey): SharpEntityList
    {
        return $this->entityManager->entityFor($entityKey)->getListOrFail();
    }

    protected function getShowInstance(string $entityKey): SharpShow
    {
        return $this->entityManager->entityFor($entityKey)->getShowOrFail();
    }
    
    protected function getFormInstance(string $entityKey): SharpForm
    {
        return $this->entityManager->entityFor($entityKey)->getFormOrFail();
        
//        if($this->isSubEntity($entityKey)) {
//            list($entityKey, $subEntityKey) = explode(':', $entityKey);
//        }
//        
//        if($entity = config("sharp.entities.$entityKey")) {
//            if(is_string($entity)) {
//                return app(app($entity)->getForm($subEntityKey ?? null));
//            } 
//            
//            $formClass = isset($subEntityKey)
//                ? ($entity["forms.$subEntityKey.form"] ?? null)
//                : ($entity["form"] ?? null);
//            
//            if($formClass) {
//                return app($formClass);
//            }
//        }
//
//        throw new SharpInvalidEntityKeyException("The form for the entity [{$entityKey}] was not found.");
    }

    protected function getDashboardInstance(string $dashboardKey): ?SharpDashboard
    {
        if(!$dashboardClass = config("sharp.dashboards.$dashboardKey.view")) {
            throw new SharpInvalidEntityKeyException("The dashboard [{$dashboardKey}] was not found.");
        }

        return app($dashboardClass);
    }

    protected function isSubEntity(string $entityKey): bool
    {
        return str_contains($entityKey, ':');
    }
}