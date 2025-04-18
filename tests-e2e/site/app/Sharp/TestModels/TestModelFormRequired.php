<?php

namespace App\Sharp\TestModels;

class TestModelFormRequired extends TestModelForm
{
    public function update($id, array $data)
    {
        $this->validate($data, [
            'text' => 'required',
            'text_localized.en' => 'required',
            'textarea' => 'required',
            'textarea_localized.en' => 'required',
            'editor_html' => 'required',
            'editor_html_localized.en' => 'required',
            'editor_markdown' => 'required',
            'number' => 'required|int|min:2',
            'date' => 'required',
            'time' => 'required',
            'date_time' => 'required',
            'check' => 'accepted',
            'tags' => 'required',
            'select_dropdown' => 'required',
            'select_dropdown_multiple' => 'required',
            'select_radio' => 'required|in:10',
            'select_checkboxes' => 'required',
            'autocomplete_local' => 'required',
            'autocomplete_remote' => 'required',
            'autocomplete_remote2' => 'required',
            'autocomplete_list' => 'required',
            'list' => 'required',
            'list.*.item' => 'required',
            'geolocation' => 'required',
            'upload' => 'required',
        ]);

        return parent::update($id, $data); // TODO: Change the autogenerated stub
    }
}
