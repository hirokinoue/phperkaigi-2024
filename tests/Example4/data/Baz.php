<?php

namespace Hirokinoue\DependencyVisualizer\Tests\Example4\data;

class Baz
{
    public function baz(): void
    {
        new Qux();
    }
}
