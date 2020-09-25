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

namespace Divity\Readme\Http\Controllers\Postman\Snippet\Targets;

/**
 * @author sankar <sankar.suda@gmail.com>
 */
abstract class TargetAbstract
{
    abstract public function generate(array $request, array $options = []): string;

    abstract public function info(): array;
}
