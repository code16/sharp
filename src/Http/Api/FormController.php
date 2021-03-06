<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpSingleForm;
use Code16\Sharp\Http\Context\Util\BreadcrumbItem;

class FormController extends ApiController
{
    public function edit(string $entityKey, string $instanceId = null)
    {
        sharp_check_ability("view", $entityKey, $instanceId);

        $form = $this->getFormInstance($entityKey);
        $this->checkFormImplementation($form, $instanceId);
        $form->buildFormConfig();

        return response()->json(
            array_merge(
                [
                    "fields" => $form->fields(),
                    "layout" => $form->formLayout(),
                    "config" => $form->formConfig(),
                    "data" => $form->instance($instanceId)
                ],
                $this->dataLocalizations($form)
            )
        );
    }

    public function create(string $entityKey)
    {
        $form = $this->getFormInstance($entityKey);

        if($form instanceof SharpSingleForm) {
            return $this->edit($entityKey);
        }

        sharp_check_ability("create", $entityKey);
        $form->buildFormConfig();

        return response()->json(
            array_merge(
                [
                    "fields" => $form->fields(),
                    "layout" => $form->formLayout(),
                    "config" => $form->formConfig(),
                    "data" => $form->newInstance()
                ],
                $this->dataLocalizations($form)
            )
        );
    }

    public function update(string $entityKey, string $instanceId = null)
    {
        sharp_check_ability("update", $entityKey, $instanceId);

        $this->validateRequest($entityKey);

        $form = $this->getFormInstance($entityKey);
        $this->checkFormImplementation($form, $instanceId);
        
        $form->updateInstance($instanceId, request()->all());
        
        return response()->json([
            "redirectUrl" => $this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem()
        ]);
    }

    public function store(string $entityKey)
    {
        $form = $this->getFormInstance($entityKey);
        $form->buildFormConfig();

        if($form instanceof SharpSingleForm) {
            // There is no creation in SingleForms
            return $this->update($entityKey);
        }

        sharp_check_ability("create", $entityKey);
        $this->validateRequest($entityKey);
        
        $instanceId = $form->storeInstance(request()->all());
        
        $previousUrl = $this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem();
        
        return response()->json([
            "redirectUrl" => $form->isDisplayShowPageAfterCreation()
                ? "{$previousUrl}/s-show/{$entityKey}/{$instanceId}"
                : $previousUrl
        ]);
    }

    public function delete(string $entityKey, string $instanceId = null)
    {
        sharp_check_ability("delete", $entityKey, $instanceId);

        $form = $this->getFormInstance($entityKey);
        $this->checkFormImplementation($form, $instanceId);

        $form->delete($instanceId);

        $entityKey = $this->isSubEntity($entityKey) ? explode(':', $entityKey)[0] : $entityKey;
        if($previousShowOfAnotherEntity = $this->currentSharpRequest->getPreviousShowFromBreadcrumbItems("!$entityKey")) {
            $redirectUrl = $this->currentSharpRequest->getUrlForBreadcrumbItem($previousShowOfAnotherEntity);
        }
        
        return response()->json([
            "redirectUrl" => $redirectUrl ?? $this->currentSharpRequest->getUrlOfPreviousBreadcrumbItem("s-list")
        ]);
    }

    protected function validateRequest(string $entityKey)
    {
        if($this->isSubEntity($entityKey)) {
            list($entityKey, $subEntityKey) = explode(':', $entityKey);
            $validatorClass = config("sharp.entities.{$entityKey}.forms.{$subEntityKey}.validator");
        } else {
            $validatorClass = config("sharp.entities.{$entityKey}.validator");
        }

        if(class_exists($validatorClass)) {
            // Validation is automatically called (FormRequest)
            app($validatorClass);
        }
    }

    protected function dataLocalizations(SharpForm $form): array
    {
        return $form->hasDataLocalizations()
            ? ["locales" => $form->getDataLocalizations()]
            : [];
    }

    protected function checkFormImplementation(SharpForm $form, ?string $instanceId)
    {
        if(!$instanceId && !$form instanceof SharpSingleForm) {
            abort(404);
        }

        if($instanceId && $form instanceof SharpSingleForm) {
            abort(404);
        }
    }
}