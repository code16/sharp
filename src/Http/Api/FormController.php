<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpSingleForm;

class FormController extends ApiController
{

    /**
     * @param string $entityKey
     * @param string|null $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function edit($entityKey, $instanceId = null)
    {
        sharp_check_ability("view", $entityKey, $instanceId);

        $form = $this->getFormInstance($entityKey);
        $this->checkFormImplementation($form, $instanceId);

        return response()->json([
            "fields" => $form->fields(),
            "layout" => $form->formLayout(),
            "data" => $form->instance($instanceId)
        ] + $this->dataLocalizations($form));
    }

    /**
     * @param string $entityKey
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function create($entityKey)
    {
        sharp_check_ability("create", $entityKey);

        $form = $this->getFormInstance($entityKey);

        return response()->json([
            "fields" => $form->fields(),
            "layout" => $form->formLayout(),
            "data" => $form->newInstance()
        ] + $this->dataLocalizations($form));
    }

    /**
     * @param string $entityKey
     * @param string|null $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormUpdateException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function update($entityKey, $instanceId = null)
    {
        sharp_check_ability("update", $entityKey, $instanceId);

        $this->validateRequest($entityKey);

        $form = $this->getFormInstance($entityKey);
        $this->checkFormImplementation($form, $instanceId);

        $form->updateInstance($instanceId, request()->all());

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $entityKey
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     * @throws \Code16\Sharp\Exceptions\Form\SharpFormUpdateException
     */
    public function store($entityKey)
    {
        sharp_check_ability("create", $entityKey);

        $this->validateRequest($entityKey);

        $form = $this->getFormInstance($entityKey);

        $form->storeInstance(request()->all());

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $entityKey
     * @param string|null $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function delete($entityKey, $instanceId = null)
    {
        sharp_check_ability("delete", $entityKey, $instanceId);

        $form = $this->getFormInstance($entityKey);
        $this->checkFormImplementation($form, $instanceId);

        $form->delete($instanceId);

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $entityKey
     */
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

    /**
     * @param SharpForm $form
     * @return array
     */
    protected function dataLocalizations(SharpForm $form)
    {
        return $form->hasDataLocalizations()
            ? ["locales" => $form->getDataLocalizations()]
            : [];
    }

    /**
     * @param SharpForm $form
     * @param string|null $instanceId
     */
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