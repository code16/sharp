<?php

namespace Code16\Sharp\Form\Eloquent\Migrations;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait MigrateContentsForSharp9
{
    protected function updateContentOf(Builder $query, string $primaryKey = 'id'): self
    {
        $columns = $query->getColumns();
        
        if(!in_array($primaryKey, $columns)) {
            throw new \Exception("You must select the primary key column ($primaryKey) to update.");
        }
        
        $contentColumns = collect($columns)->diff([$primaryKey])->toArray();
        
        if(empty($contentColumns)) {
            throw new \Exception('You must select at least one column to update.');
        }
        
        $rows = $query
            ->tap(function (Builder $query) use ($contentColumns) {
                collect($contentColumns)->each(fn ($column) =>
                    $query
                        ->orWhere($column, 'like', '%<x-sharp-image%')
                        ->orWhere($column, 'like', '%<x-sharp-file%')
                    );
            })
            ->get();
        
        foreach ($rows as $row) {
            DB::table($query->from)
                ->where($primaryKey, $row->{$primaryKey})
                ->update(
                    Arr::mapWithKeys($contentColumns, function ($column) use ($row) {
                        return [
                            $column => $this->updateContent($row->{$column}),
                        ];
                    })
                );
        }
        
        return $this;
    }
    
    protected function updateContent(?string $content): ?string
    {
        if(is_null($content)) {
            return null;
        }
        
        $result = preg_replace_callback(
            '/<(x-sharp-file) ([^>]+)>/m',
            function ($matches) {
                $tag = $matches[1];
                $attributes = $matches[2];
                $name = preg_match('/name="([^"]+)"/', $attributes, $matchesName) ? $matchesName[1] : '';
                $disk = preg_match('/disk="([^"]+)"/', $attributes, $matchesDisk) ? $matchesDisk[1] : '';
                $path = preg_match('/path="([^"]+)"/', $attributes, $matchesPath) ? $matchesPath[1] : '';
                
                $filters = [];
                if(preg_match('/filter-crop="([^"]+)"/', $attributes, $matchesCrop)) {
                    $cropValues = explode(',', $matchesCrop[1]);
                    $filters['crop'] = [
                        'x' => $cropValues[0],
                        'y' => $cropValues[1],
                        'width' => $cropValues[2],
                        'height' => $cropValues[3],
                    ];
                }
                
                if (preg_match('/filter-rotate="([^"]+)"/', $attributes, $matchesRotate)) {
                    $filters['rotate'] = [
                        'angle' => $matchesRotate[1],
                    ];
                }
                
                return sprintf(
                    '<%s file="%s">',
                    $tag,
                    e(json_encode(array_filter([
                        'name' => $name,
                        'disk' => $disk,
                        'path' => $path,
                        'filters' => $filters,
                    ]))),
                );
            },
            $content
        );
        
        return is_null($result) ? $content : $result;
    }
}
