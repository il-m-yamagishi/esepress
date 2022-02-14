<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Contracts\Container;

use LogicException;

/**
 * It is already bound to container, so it cannot bind
 */
class AlreadyBoundException extends LogicException
{
}
