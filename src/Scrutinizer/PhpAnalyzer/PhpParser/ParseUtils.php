<?php

namespace Scrutinizer\PhpAnalyzer\PhpParser;

use JMS\PhpManipulator\PhpParser\BlockNode;

abstract class ParseUtils
{
    public static function parser()
    {
        return new \PHPParser_Parser(new \PHPParser_Lexer_Emulative);
    }

    public static function parse($code)
    {
        $parser = ParseUtils::parser();
        $ast = $parser->parse($code);

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new NodeVisitor\NormalizingNodeVisitor());
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver());
        $ast = $traverser->traverse($ast);

        switch (count($ast)) {
            case 0:
                $ast = new BlockNode(array());
                break;

            case 1:
                $ast = $ast[0];
                break;

            default:
                $ast = new BlockNode($ast);
        }

        // This is currently only available when using the schmittjoh/PHP-Parser fork.
        if (class_exists('PHPParser_NodeVisitor_NodeConnector')) {
            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor(new \PHPParser_NodeVisitor_NodeConnector());
            $traverser->traverse(array($ast));
        }

        return $ast;
    }

    private final function __construct() { }
}
