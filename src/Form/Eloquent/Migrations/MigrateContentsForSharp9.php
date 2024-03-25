<?php

namespace Code16\Sharp\Form\Eloquent\Migrations;

use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToRetrieveMetadata;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

trait MigrateContentsForSharp9
{
    use InteractsWithIO;
    
    protected function updateContentOf(Builder $query, array $contentColumns, string $primaryKey = 'id', bool $fetchMissingDataFromFileSystem = true): self
    {
        $this->input ??= new StringInput('');
        $this->output ??= new OutputStyle($this->input, new ConsoleOutput());
        
        $rows = $query
            ->tap(function (Builder $query) use ($contentColumns) {
                collect($contentColumns)->each(fn ($column) => $query
                        ->orWhere($column, 'like', '%<x-sharp-image%')
                        ->orWhere($column, 'like', '%<x-sharp-file%')
                );
            })
            ->get();

        foreach ($rows as $row) {
            $data = [];
            foreach ($contentColumns as $column) {
                $data[$column] = $this->updateContent(
                    $row->{$column},
                    $fetchMissingDataFromFileSystem,
                    sprintf("%s[%s][%s]", $query->from, $row->{$primaryKey}, $column),
                );
            }
            
            DB::table($query->from)
                ->where($primaryKey, $row->{$primaryKey})
                ->update($data);
        }

        return $this;
    }

    protected function updateContent(?string $content, bool $fetchMissingDataFromFileSystem = true, string $rowLocation = ''): ?string
    {
        if (is_null($content)) {
            return null;
        }

        $result = preg_replace_callback(
            '/<(x-sharp-file|x-sharp-image) ([^>]+)>/m',
            function ($matches) use ($fetchMissingDataFromFileSystem, $rowLocation) {
                $tag = $matches[1];
                $attributes = $matches[2];
                $name = preg_match('/name="([^"]+)"/', $attributes, $matchesName) ? $matchesName[1] : '';
                $disk = preg_match('/disk="([^"]+)"/', $attributes, $matchesDisk) ? $matchesDisk[1] : '';
                $path = preg_match('/path="([^"]+)"/', $attributes, $matchesPath) ? $matchesPath[1] : '';

                $filters = [];
                if (preg_match('/filter-crop="([^"]+)"/', $attributes, $matchesCrop)) {
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
                
                if($fetchMissingDataFromFileSystem) {
                    try {
                        $size = Storage::disk($disk)->size($path);
                        $mime_type = Storage::disk($disk)->mimeType($path);
                    } catch (UnableToRetrieveMetadata $e) {
                        $this->warn($e->getMessage()."Found in $rowLocation");
                    }
                }

                return sprintf(
                    '<%s file="%s">',
                    $tag,
                    e(json_encode(array_filter([
                        'file_name' => $path,
                        'size' => $size ?? null,
                        'mime_type' => $mime_type ?? null,
                        'disk' => $disk,
                        'filters' => count($filters) ? $filters : null,
                    ], fn ($value) => $value !== null))),
                );
            },
            $content
        );

        return is_null($result) ? $content : $result;
    }
}
