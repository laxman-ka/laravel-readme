<?php

declare (strict_types = 1);

/*
 * This file is part of the Speedwork package.
 *
 * (c) Sankar <sankar.suda@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace Divity\Readme\Http\Controllers\Postman\Snippet;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
class Builder
{
    private $indentation;
    private $lineJoin;
    private $code = [];

    public function __construct($indentation = '  ', $lineJoin = "\n")
    {
        $this->indentation = $indentation;
        $this->lineJoin    = $lineJoin;
    }

    public function setIndentation($indentation = '  ')
    {
        $this->indentation = $indentation;

        return $this;
    }

    public function line($line, $indentation = 2)
    {
        if (null === $indentation) {
            return null;
        }

        if (is_string($indentation)) {
            $line        = sprintf($line, $indentation);
            $indentation = 0;
        }

        $indentation = is_numeric($indentation) ? $indentation : 1;

        $lineIndentation = '';

        while ($indentation) {
            $lineIndentation .= $this->indentation;
            --$indentation;
        }

        return $lineIndentation . $line;
    }

    /**
     * Invoke buildLine() and add the line at the top of current lines.
     *
     * @param {number} [indentationLevel=0] Desired level of indentation for this line
     * @param {string} line Line of code
     *
     * @return {this}
     */
    public function unshift($line, $indentation = 0)
    {
        array_unshift($this->code, $this->line($line, $indentation));

        return $this;
    }

    /**
     * Invoke buildLine() and add the line at the bottom of current lines.
     *
     * @param {number} [indentation=0] Desired level of indentation for this line
     * @param {string} line Line of code
     *
     * @return {this}
     */
    public function push($line, $indentation = 0)
    {
        array_push($this->code, $this->line($line, $indentation));

        return $this;
    }

    /**
     * Add an empty line at the end of current lines.
     *
     * @return {this}
     */
    public function blank()
    {
        array_push($this->code, null);

        return $this;
    }

    /**
     * Concatenate all current lines using the given lineJoin.
     *
     * @return {string}
     */
    public function join()
    {
        return implode($this->lineJoin, $this->code);
    }
}
