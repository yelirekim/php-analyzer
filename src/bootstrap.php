<?php

foreach([
    'PhpParser\NodeVisitor\NodeConnector' => 'PHPParser_NodeVisitor_NodeConnector',
    'PhpParser\Finder' => 'PHPParser_Finder',
] as $current => $original) {
    class_alias($current, $original);
}
