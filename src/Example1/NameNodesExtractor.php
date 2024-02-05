<?php declare(strict_types=1);

namespace Hirokinoue\DependencyVisualizer\Example1;

use InvalidArgumentException;
use PhpParser\{Node, NodeFinder, NodeTraverser, ParserFactory};
use PhpParser\Node\{Name, Stmt};
use PhpParser\NodeVisitor\NameResolver;

final class NameNodesExtractor {
    /**
     * @var Stmt[]
     */
    private array $stmts;

    /**
     * @param Stmt[] $stmts
     */
    private function __construct(array $stmts) {
        $this->stmts = $stmts;
    }

    public static function create(string $filePath): self {
        $fileContent = file_get_contents($filePath);
        $code = ($fileContent === false) ? '' : $fileContent;
        $parser = (new ParserFactory())->createForHostVersion();
        $stmts = $parser->parse($code);
        if ($stmts === null) {
            throw new InvalidArgumentException('No ast found.');
        }
        return new self($stmts);
    }

    /**
     * @return Node[]
     */
    private function resolveName(): array
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        return $traverser->traverse($this->stmts);
    }

    /**
     * @return Name[]
     */
    public function all(): array
    {
        // 名前を解決する
        $namedNodes = $this->resolveName();

        // Nameノードでフィルターする
        $nodeFinder = new NodeFinder();
        return $nodeFinder->findInstanceOf($namedNodes, Name::class);
    }

    /**
     * @template TNode as Node
     * @param class-string<TNode> $className
     * @return Name[]
     */
    public function filterBy(string $className): array
    {
        // 名前を解決する
        $namedNodes = $this->resolveName();

        // 指定されたノードでフィルターする
        $nodeFinder = new NodeFinder();
        $filteredNodes = $nodeFinder->findInstanceOf($namedNodes, $className);

        // 指定されたノードのサブノードに含まれるNameノードを取得する
        $nameNodes = [];
        foreach ($filteredNodes as $filteredNode) {
            foreach ($filteredNode->getSubNodeNames() as $name) {
                $subNode = $filteredNode->$name;
                if ($subNode instanceof Name) {
                    $nameNodes[] = $subNode;
                }
            }
        }
        return $nameNodes;
    }
}
