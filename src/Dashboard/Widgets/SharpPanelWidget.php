<?php

namespace Code16\Sharp\Dashboard\Widgets;

class SharpPanelWidget extends SharpWidget
{

    /**
     * @var string
     */
    protected $inlineTemplate;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        $widget = new static($key, 'panel');

        return $widget;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "template" => $this->inlineTemplate
        ]);
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplatePath(string $template)
    {
        return $this->setInlineTemplate(
            file_get_contents(resource_path("views/" . $template))
        );
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setInlineTemplate(string $template)
    {
        $this->inlineTemplate = $template;

        return $this;
    }

    /**
     * Return specific validation rules.
     *
     * @return array
     */
    protected function validationRules()
    {
        return [
            "template" => "required"
        ];
    }

}