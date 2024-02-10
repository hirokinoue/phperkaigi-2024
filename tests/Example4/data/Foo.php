<?php

namespace Hirokinoue\DependencyVisualizer\Tests\Example4\data;

class Foo
{
    public function foo(): void
    {
        new Bar();
        new Baz();
    }
}
