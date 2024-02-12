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
        
        if(empty($columns)) {
            throw new \Exception('You must select at least one column to update.');
        }
        
        if(!in_array($primaryKey, $columns)) {
            throw new \Exception("You must select the primary key column ($primaryKey) to update.");
        }
        
        $rows = $query
            ->when(!in_array($primaryKey, $columns), fn (Builder $query) => $query
                ->addSelect($primaryKey)
            )
            ->tap(function (Builder $query) use ($columns) {
                collect($columns)->each(fn ($column) =>
                $query
                    ->orWhere($column, 'like', '%filter-crop="%')
                    ->orWhere($column, 'like', '%filter-rotate="%')
                );
            })
            ->get();
        
        $contentColumns = collect($columns)->except($primaryKey)->toArray();
        
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
            '/<x-sharp-image ([^>]+)>/m',
            function ($matches) {
                $filters = [];
                $name = preg_match('/name="([^"]+)"/', $matches[1], $matchesName) ? $matchesName[1] : '';
                $disk = preg_match('/disk="([^"]+)"/', $matches[1], $matchesDisk) ? $matchesDisk[1] : '';
                $path = preg_match('/path="([^"]+)"/', $matches[1], $matchesPath) ? $matchesPath[1] : '';
                
                if(preg_match('/filter-crop="([^"]+)"/', $matches[1], $matchesCrop)) {
                    $cropValues = explode(',', $matchesCrop[1]);
                    $filters['crop'] = [
                        'x' => $cropValues[0],
                        'y' => $cropValues[1],
                        'width' => $cropValues[2],
                        'height' => $cropValues[3],
                    ];
                }
                
                if (preg_match('/filter-rotate="([^"]+)"/', $matches[1], $matchesRotate)) {
                    $filters['rotate'] = [
                        'angle' => $matchesRotate[1],
                    ];
                }
                
                if(!empty($filters)) {
                    return sprintf(
                        '<x-sharp-image file="%s">',
                        e(json_encode(array_filter([
                            'name' => $name,
                            'disk' => $disk,
                            'path' => $path,
                            'filters' => $filters,
                        ]))),
                    );
                }
                
                return $matches[0];
            },
            $content
        );
        
        return is_null($result) ? $content : $result;
    }
}
