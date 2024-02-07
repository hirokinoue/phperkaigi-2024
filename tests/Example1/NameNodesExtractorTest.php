<?php declare(strict_types=1);

namespace Hirokinoue\DependencyVisualizer\Tests\Example1;

use Hirokinoue\DependencyVisualizer\Example1\NameNodesExtractor;
use PhpParser\Node\Param;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\GroupUse;
use PHPUnit\Framework\TestCase;

final class NameNodesExtractorTest extends TestCase
{
    /**
     * @dataProvider dataNameResolverが修飾名を取得できること
     * @noinspection NonAsciiCharacters
     * @param array<int, string> $expectedNames
     */
    public function testNameResolverが修飾名を取得できること(string $dataFileName, array $expectedNames): void
    {
        // given
        $sut = NameNodesExtractor::create(__DIR__ . '/../data/' . $dataFileName . '.php');

        // when
        $result = $sut->all();

        // then
        $this->assertSame(count($expectedNames), count($result));

        foreach ($result as $i => $nameNode) {
            $this->assertSame($expectedNames[$i], $nameNode->name);
        }
    }

    /**
     * @noinspection NonAsciiCharacters
     * @return array<string, array<string|array<int, string>>>
     */
    public function dataNameResolverが修飾名を取得できること(): array
    {
        $allVersion = [
            'クラスへのアクセス_クラス定数_名前空間あり' => [
                'ClassConstFetchNode/ClassConstantWithNamespace', ['Baz\Foo'],
            ],
            'クラスへのアクセス_クラス定数_名前空間なし' => [
                'ClassConstFetchNode/ClassConstantWithoutNamespace', ['FOO'],
            ],
            'クラスへのアクセス_特殊なクラス名' => [
                'SpecialClassName/Foo', [
                    'static',
                    'self',
                    'parent',
                ],
            ],
            '定数へのアクセス' => [
                'ConstFetchNode/Constant', ['FOO'],
            ],
            'キーワードへのアクセス' => [
                'ConstFetchNode/Keyword', [
                    'true',
                    'null',
                ],
            ],
            '関数コール_関数名が式じゃない' => [
                'FuncCallNode/NameIsName', ['phpversion'],
            ],
            '名前空間' => [
                'NamespaceNode/FooBar', ['Foo\Bar'],
            ],
            'インポート' => [
                'UseUseNode/FooBar', ['Foo\Bar'],
            ],
            'クラスの宣言_継承_実装あり' => [
                'ClassNode/FooExtendsImplements', ['Bar', 'Baz'],
            ],
            '関数の宣言_戻り値の型あり' => [
                'FunctionNode/FooWithReturnType', ['Foo'],
            ],
        ];
        if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
            $php8 = [
                'アトリビュート' => [
                    'AttributeNode/Foo', [
                        'Attribute',
                        'Foo',
                    ],
                ],
            ];
        } else {
            $php8 = [];
        }
        return array_merge($allVersion, $php8);
    }

    /**
     * @dataProvider dataNameノードが存在しないこと
     * @noinspection NonAsciiCharacters
     */
    public function testNameノードが存在しないこと(string $dataFileName): void
    {
        // given
        $sut = NameNodesExtractor::create(__DIR__ . '/../data/' . $dataFileName . '.php');

        // when
        $result = $sut->all();

        // then
        $this->assertEmpty($result);
    }

    /**
     * @noinspection NonAsciiCharacters
     * @return array<string, array<int, string>>
     */
    public function dataNameノードが存在しないこと(): array
    {
        return [
            'クラスの宣言' => [
                'ClassNode/Foo',
            ],
            '関数の宣言_戻り値の型なし' => [
                'FunctionNode/Foo',
            ],
            '定数の宣言_名前空間なし' => [
                'ConstNode/Constant',
            ],
            '関数コール_関数名が式' => [
                'FuncCallNode/NameIsExpr',
            ],
        ];
    }

    /**
     * @dataProvider dataNameResolverが修飾名を取得できることをノード毎に確認できること
     * @noinspection NonAsciiCharacters
     * @template TNode as \PhpParser\Node
     * @param class-string<TNode> $className
     * @param array<int, string> $expectedNames
     */
    public function testNameResolverが修飾名を取得できることをノード毎に確認できること(string $dataFileName, string $className, array $expectedNames): void
    {
        // given
        $sut = NameNodesExtractor::create(__DIR__ . '/../data/' . $dataFileName . '.php');

        // when
        $result = $sut->filterBy($className);

        // then
        $this->assertSame(count($expectedNames), count($result));

        foreach ($result as $i => $nameNode) {
            $this->assertSame($expectedNames[$i], $nameNode->name);
        }
    }

    /**
     * @noinspection NonAsciiCharacters
     * @return array<string, array<string|class-string|array<int, string>>>
     */
    public function dataNameResolverが修飾名を取得できることをノード毎に確認できること(): array
    {
        return [
            'クラスへのアクセス_クラス定数' => [
                'ClassConstFetchNode/ClassConstantWithUse',
                ClassConstFetch::class, [
                    'Baz\Foo',
                ],
            ],
            'インポート_グループ化された宣言' => [
                'GroupUseNode/Foo',
                GroupUse::class, [
                    'Foo',
                    'Foo\Qux',
                ],
            ],
            '関数のパラメータ' => [
                'ParamNode/InFunction',
                Param::class, [
                    'Foo',
                ],
            ],
        ];
    }
}
