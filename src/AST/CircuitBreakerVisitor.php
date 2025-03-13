<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\AST;

use Hyperf\Di\Aop\Ast;
use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr\CallLike;
use PhpParser\NodeVisitorAbstract;

class CircuitBreakerVisitor extends NodeVisitorAbstract
{
    protected static Ast $ast;

    protected int $level = 0;

    protected ?CallLike $callLike = null;

    public function __construct()
    {
        self::$ast ??= new Ast();
    }

    public function enterNode(Node $node): ?Node
    {
        if ($node instanceof Node\Expr\MethodCall) {
            ++$this->level;
        }

        if ($callLike = $this->commentsToCallLike($node)) {
            $this->callLike = $callLike;
        }

        return null;
    }

    public function leaveNode(Node $node): Node
    {
        if (! $node instanceof Node\Expr\MethodCall || --$this->level || ! $this->callLike) {
            return $node;
        }

        $node = $this->appendOriginCall($this->callLike, $node);

        $this->callLike = null;
        return $node;
    }

    // TODO: register annotation
    protected function commentsToCallLike(Node $node): ?CallLike
    {
        if (! $comments = $node->getAttribute('comments')) {
            return null;
        }
        foreach ($comments as $comment) {
            if (! $comment instanceof Comment\Doc) {
                continue;
            }
            if (! preg_match('#@(Timeout.*?\))#', $comment->getText(), $matches)) {
                continue;
            }
            return self::$ast->parse("<?php {$matches[1]}; ")[0]->expr;
        }
        return null;
    }

    private function appendOriginCall(CallLike $callLike, CallLike $originCall): CallLike
    {
        if ($originCall instanceof Node\Expr\New_) {
            echo 'Warning: NEW expression is not supported yet.' . PHP_EOL;
            return $originCall;
        }
        $args = $originCall->args;
        $originCall->args = [new Node\VariadicPlaceholder()];
        $closureCall = new Node\Arg($originCall);
        array_push($callLike->args, $closureCall, ...$args);
        return $callLike;
    }
}
