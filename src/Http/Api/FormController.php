<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Form\SharpForm;
use Illuminate\Routing\Controller;

class FormController extends Controller
{

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($entityKey, $instanceId)
    {
        $form = $this->getFormInstance($entityKey);

        return response()->json([
            "fields" => $form->fields(),
            "layout" => $form->formLayout(),
            "data" => $form->instance($instanceId)
        ]);
    }

    /**
     * @param string $entityKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function create($entityKey)
    {
        $form = $this->getFormInstance($entityKey);

        return response()->json([
            "fields" => $form->fields(),
            "layout" => $form->formLayout(),
            "data" => $form->newInstance()
        ]);
    }

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $instanceId)
    {
        $this->validateRequest($entityKey);

        $form = $this->getFormInstance($entityKey);

        $form->update($instanceId, $this->requestData($form));

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $entityKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($entityKey)
    {
        $this->validateRequest($entityKey);

        $form = $this->getFormInstance($entityKey);

        $form->store(request()->only($form->getFieldKeys()));

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $entityKey
     * @return SharpForm
     */
    protected function getFormInstance(string $entityKey): SharpForm
    {
        return app(config("sharp.entities.{$entityKey}.form"));
    }

    /**
     * @param string $entityKey
     */
    protected function validateRequest(string $entityKey)
    {
        $validatorClass = config("sharp.entities.{$entityKey}.validator");

        if(class_exists($validatorClass)) {
            // Validation is automatically called (FormRequest)
            app($validatorClass);
        }
    }

    /**
     * @param $form
     * @return array
     */
    protected function requestData($form): array
    {
        return collect(request()->all())->filter(function ($item, $key) use ($form) {
            return in_array($key, $form->getFieldKeys());
        })->all();
    }
}