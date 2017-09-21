<?php

namespace Resomedia\DoctrineEncryptBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Class DecryptFunction
 * @package Resomedia\DoctrineEncryptBundle\DQL
 */
class DecryptFunction extends FunctionNode
{
    /**
     * @var string $stringCrypt
     */
    protected $stringCrypt;

    /**
     * @var string $key
     */
    protected $key;

    /**
     * @var array $iv
     */
    protected $iv;

    /**
     * Parse DQL Function
     * @param Parser $parser
     */
    public function parse(Parser $parser) {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->stringCrypt = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->key = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->iv = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Get SQL
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker) {
        return "AES_DECRYPT(SUBSTRING(" . $this->stringCrypt->field . ", 6), '" . $this->key->value . "', '" . $this->iv->value . "')";
    }
}