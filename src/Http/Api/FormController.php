<?php

namespace Code16\Sharp\Http\Api;

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
        $form = $this->getFormDataInstance($key);

        return response()->json([
            "fields" => [],
            "layout" => [],
            "data" => $form->get($id)
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

        $this->getFormDataInstance($key)
            ->update($id, request()->all()); // TODO ->only on actual presented fields

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($key)
    {
        $this->validateRequest($key);

        $this->getFormDataInstance($key)
            ->store(request()->all()); // TODO ->only on actual presented fields

        return response()->json(["ok" => true]);
    }

    /**
     * @param string $key
     * @return SharpFormData
     */
    protected function getFormDataInstance(string $key): SharpFormData
    {
        $formClass = config("sharp.entities.{$key}.data");
        return app($formClass);
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