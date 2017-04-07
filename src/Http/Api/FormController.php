<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpFormData;
use Illuminate\Routing\Controller;

class FormController extends Controller
{

    /**
     * @param string $key
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($key, $id)
    {
        $form = $this->getFormInstance($key);

        return response()->json([
            "fields" => $form->fields(),
            "layout" => $form->formLayout(),
            "data" => $form->instance($id)
        ]);
    }

    /**
     * @param string $key
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($key, $id)
    {
        $this->validateRequest($key);

        $form = $this->getFormInstance($key);

        $form->update($id, request()->only($form->getFieldKeys()));

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($key)
    {
        $this->validateRequest($key);

        $form = $this->getFormInstance($key);

        $form->store(request()->only($form->getFieldKeys()));

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $key
     * @return SharpForm
     */
    protected function getFormInstance(string $key): SharpForm
    {
        return app(config("sharp.entities.{$key}.form"));
    }

    /**
     * @param string $key
     */
    protected function validateRequest(string $key)
    {
        $validatorClass = config("sharp.entities.{$key}.validator");

        if(class_exists($validatorClass)) {
            // Validation is automatically called (FormRequest)
            app($validatorClass);
        }
    }
}