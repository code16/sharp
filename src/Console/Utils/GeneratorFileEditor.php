<?php

namespace Code16\Sharp\Console\Utils;

final class GeneratorFileEditor
{
    public function __construct(private string $filePath) {}

    public function ensureUseStatement(string $className): void
    {
        $useStatement = 'use '.$className.';';
        if ($this->contains($useStatement)) {
            return;
        }

        $this->replace(PHP_EOL.'class ', $useStatement.PHP_EOL.PHP_EOL.'class ');
    }

    public function ensureMethodArrayContains(string $methodSignature, string $className): void
    {
        $needle = $className.'::class';
        if ($this->contains($needle)) {
            return;
        }

        $search = $methodSignature.PHP_EOL.'    {'.PHP_EOL.'        return ['.PHP_EOL;
        $replace = $search.'            '.$className.'::class,'.PHP_EOL;

        $this->replace($search, $replace);
    }

    public function ensureMethodChainContains(string $methodSignature, string $chainCall): void
    {
        if ($this->contains($chainCall)) {
            return;
        }

        $search = $methodSignature.PHP_EOL.'    {'.PHP_EOL.'        $this'.PHP_EOL;
        $replace = $search.'            '.$chainCall.PHP_EOL;

        $this->replace($search, $replace);
    }

    private function contains(string $needle): bool
    {
        return str_contains($this->read(), $needle);
    }

    private function read(): string
    {
        if (! is_file($this->filePath) || ! is_readable($this->filePath)) {
            return '';
        }

        $content = file_get_contents($this->filePath);

        return $content === false ? '' : $content;
    }

    private function replace(string $search, string $replace): void
    {
        if (! is_file($this->filePath) || ! is_writable($this->filePath)) {
            return;
        }

        $content = $this->read();

        if ($content === '' || ! str_contains($content, $search)) {
            return;
        }

        $updatedContent = str_replace($search, $replace, $content);

        if ($updatedContent === $content) {
            return;
        }

        file_put_contents($this->filePath, $updatedContent);
    }
}
