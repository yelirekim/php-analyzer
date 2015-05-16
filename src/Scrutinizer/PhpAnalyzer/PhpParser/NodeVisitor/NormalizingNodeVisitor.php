<?php

namespace Scrutinizer\PhpAnalyzer\PhpParser\NodeVisitor;

use JMS\PhpManipulator\PhpParser\BlockNode;

class NormalizingNodeVisitor extends \PHPParser_NodeVisitorAbstract
{
    private $imports;

    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->imports = array();
        } else if ($node instanceof \PHPParser_Node_Stmt_Use) {
            foreach ($node->uses as $use) {
                assert($use instanceof \PHPParser_Node_Stmt_UseUse);
                $this->imports[$use->alias] = implode("\\", $use->name->parts);
            }
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        if (isset($node->stmts)) {
            $block = new BlockNode($node->stmts);
            $block->setLine($node->getLine());
            $node->stmts = $block;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $node->setAttribute('imports', $this->imports);
        }
    }
}
