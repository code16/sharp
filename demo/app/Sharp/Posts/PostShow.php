<?php

namespace App\Sharp\Posts;

use App\Models\Post;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Links\LinkToShowPage;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;

class PostShow extends SharpShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(SharpShowTextField::make("title_fr")->setLabel("Title (FR)"))
            ->addField(SharpShowTextField::make("title_en")->setLabel("Title (EN)"))
            ->addField(SharpShowTextField::make("content_fr")->collapseToWordCount(30))
            ->addField(SharpShowTextField::make("content_en")->collapseToWordCount(30))
            ->addField(SharpShowTextField::make("author")->setLabel("Author"))
            ->addField(SharpShowPictureField::make("cover"))
            ->addField(
                SharpShowEntityListField::make("blocks", "blocks")
                    ->setLabel("Blocks")
                    ->hideFilterWithValue("post", function ($instanceId) {
                        return $instanceId;
                    })
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection("", function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column
                            ->withSingleField("title_fr")
                            ->withSingleField("title_en")
                            ->withSingleField("author");
                    })
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withSingleField("cover");
                    });
            })
            ->addSection("Content (FR)", function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withSingleField("content_fr");
                    });
            })
            ->addSection("Content (EN)", function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column->withSingleField("content_en");
                    });
            })
            ->addEntityListSection("blocks");
    }

    public function buildShowConfig(): void
    {
        $this->configureBreadcrumbCustomLabelAttribute("title_fr");
    }

    public function find(mixed $id): array
    {
        return $this
            ->setCustomTransformer("title_fr", function ($value, $instance) {
                return $instance->getTranslation("title", "fr");
            })
            ->setCustomTransformer("title_en", function ($value, $instance) {
                return $instance->getTranslation("title", "en");
            })
            ->setCustomTransformer("content_fr", function ($value, $instance) {
                return $instance->getTranslation("content", "fr");
            })
            ->setCustomTransformer("content_en", function ($value, $instance) {
                return $instance->getTranslation("content", "en");
            })
            ->setCustomTransformer("author", function ($value, $instance) {
                return $instance->author_id
                    ? LinkToShowPage::make("users", $instance->author_id)
                        ->renderAsText($instance->author->name)
                    : null;
            })
            ->setCustomTransformer("cover", new SharpUploadModelThumbnailUrlTransformer(300))
            ->transform(Post::findOrFail($id));
    }
}