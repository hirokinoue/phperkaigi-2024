<?php declare(strict_types=1);

namespace Hirokinoue\DependencyVisualizer\Example4;

final class DiagramUnit
{
    private string $className;
    /**
     * @var DiagramUnit[] $classesDirectlyDependsOn
     */
    private array $classesDirectlyDependsOn = [];

    public function __construct(string $className) {
        $this->className = $className;
    }

    public function push(DiagramUnit $other): void
    {
        $this->classesDirectlyDependsOn[] = $other;
    }

    public function className(): string {
        return $this->className;
    }

    /**
     * @return DiagramUnit[]
     */
    public function subClasses(): array {
        return $this->classesDirectlyDependsOn;
    }
}

