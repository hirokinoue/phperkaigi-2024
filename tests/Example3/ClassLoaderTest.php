<?php declare(strict_types=1);

namespace Hirokinoue\DependencyVisualizer\Tests\Example3;

use Hirokinoue\DependencyVisualizer\Example3\ClassLoader;
use PHPUnit\Framework\TestCase;

final class ClassLoaderTest extends TestCase
{
    /**
     * @dataProvider data対象に応じてロードできること
     * @noinspection NonAsciiCharacters
     */
    public function test対象に応じてロードできること(string $qualifiedName, string $expectedQualifiedClassName, string $expectedCode): void
    {
        // given
        $sut = ClassLoader::create($qualifiedName);

        // when
        $className = $sut->className();
        $code = $sut->content();

        // then
        $this->assertSame($expectedQualifiedClassName, $className);
        $this->assertSame($expectedCode, $code);
    }

    /**
     * @noinspection NonAsciiCharacters
     * @return array<string, array<int, string>>
     */
    public function data対象に応じてロードできること(): array
    {
        $userDefinedClass = <<<CODE
<?php
namespace Hirokinoue\DependencyVisualizer\Tests\Example3\data;
class UserDefinedClass{}

CODE;
        return [
            'ユーザー定義クラスはクラス名とコードが取得できる' => [
                'Hirokinoue\DependencyVisualizer\Tests\Example3\data\UserDefinedClass',
                'Hirokinoue\DependencyVisualizer\Tests\Example3\data\UserDefinedClass',
                $userDefinedClass,
            ],
            '内部クラスはクラス名のみ取得できる' => [
                'Exception',
                'Exception',
                '',
            ],
            '定数はクラス名もコードも取得できない' => [
                'FOO',
                '',
                '',
            ],
            'キーワードはクラス名もコードも取得できない' => [
                'true',
                '',
                '',
            ],
        ];
    }
}
