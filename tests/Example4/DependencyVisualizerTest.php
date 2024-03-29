<?php declare(strict_types=1);

namespace Hirokinoue\DependencyVisualizer\Tests\Example4;

use Hirokinoue\DependencyVisualizer\Example4\DependencyVisualizer;
use Hirokinoue\DependencyVisualizer\Example4\StringExporter;
use PHPUnit\Framework\TestCase;

final class DependencyVisualizerTest extends TestCase
{
    /**
     * @dataProvider data分析結果をテキスト形式で出力できること
     * @noinspection NonAsciiCharacters
     */
    public function test分析結果をテキスト形式で出力できること(string $path, string $expected): void
    {
        // given
        $sut = DependencyVisualizer::create($path);

        // when
        $stringExporter = new StringExporter();
        $result = $stringExporter->export($sut->analyze());

        // then
        $this->assertSame($expected, $result);
    }

    /**
     * @noinspection NonAsciiCharacters
     * @return array<string, array<int, string>>
     */
    public function data分析結果をテキスト形式で出力できること(): array
    {
        $rootIsClass = <<<RESULT
\Hirokinoue\DependencyVisualizer\Tests\Example4\data\Foo
  \Hirokinoue\DependencyVisualizer\Tests\Example4\data\Bar
  \Hirokinoue\DependencyVisualizer\Tests\Example4\data\Baz
    \Hirokinoue\DependencyVisualizer\Tests\Example4\data\Qux

RESULT;
        $rootIsNotClass = <<<RESULT
root
  \Hirokinoue\DependencyVisualizer\Tests\Example4\data\Bar
  \Hirokinoue\DependencyVisualizer\Tests\Example4\data\Baz
    \Hirokinoue\DependencyVisualizer\Tests\Example4\data\Qux

RESULT;
        return [
            '始点がクラスの時ルートがクラス名' => [
                __DIR__ . '/data/Foo.php',
                $rootIsClass,
            ],
            '始点がクラスではない時ルートがroot' => [
                __DIR__ . '/data/foo.php',
                $rootIsNotClass,
            ],
        ];
    }
}
