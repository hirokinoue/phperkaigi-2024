<?php

namespace Hirokinoue\DependencyVisualizer\Tests\Example4\data;

class RootClass {
    public function foo(\Error $e): void
    {
        new Bar();
        new Baz();
        new EmptyClass();
        Foo;
        new \stdClass;
    }
}
