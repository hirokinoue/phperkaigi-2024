<?php declare(strict_types=1);

namespace Hirokinoue\DependencyVisualizer\Example3;

use ReflectionClass;

final class ClassLoader
{
    private string $qualifiedClassName;
    private string $content;

    private function __construct(string $qualifiedClassName, string $content) {
        $this->qualifiedClassName = $qualifiedClassName;
        $this->content = $content;
    }

    public static function create(string $qualifiedName): self
    {
        try {
            // ReflectionClassにかけてみないと存在するクラスなのかどうかがわからないため
            // $qualifiedNameがclass-stringであることを保証できない
            /** @phpstan-ignore-next-line */
            $reflector = new ReflectionClass($qualifiedName);
        }
        catch (\ReflectionException $r) {
            // $qualifiedNameがクラス名ではないケースやクラスのファイルはあるが中身が無いケース
            return new self('', '');
        }

        $path = ($reflector->getFileName() === false) ? '' : $reflector->getFileName();
        $qualifiedClassName = empty($reflector->name) ? '' : $reflector->name;
        $code = self::readFile($path);

        // 定義済みクラスは$codeが空
        return new self($qualifiedClassName, $code);
    }

    private static function readFile(string $path): string {
        if ($path === '') {
            return '';
        }

        $content = \file_get_contents($path);
        if ($content === false) {
            return '';
        }

        return $content;
    }

    public function className(): string {
        return $this->qualifiedClassName;
    }

    public function content(): string {
        return $this->content;
    }

    public function isClass(): bool {
        return $this->qualifiedClassName !== '';
    }

    public function codeNotFound(): bool {
        return $this->content === '';
    }
}
