<?php

/**
 * @author Masaru Yamagishi <yamagishi.iloop@gmail.com>
 * @copyright 2022 Masaru Yamagishi
 * @license Apache License 2.0
 */

declare(strict_types=1);

namespace Semplice\Bootstrap;

use Closure;
use Error;
use ErrorException;
use Psr\Log\LoggerInterface;
use Semplice\Contracts\Bootstrap\FatalError;
use Semplice\Contracts\Bootstrap\OutofMemoryError;
use Throwable;

class ErrorHandler
{
    /**
     * for Out of memory
     *
     * inspired by [Symfony](https://github.com/symfony/symfony/blob/cbac3133c5875b28addebf2741593ec1e4048670/src/Symfony/Component/ErrorHandler/ErrorHandler.php#L114)
     * @var string
     */
    protected static string $reserved_memory = '';

    /** @var callable|null $previous_error_handler */
    protected $previous_error_handler = null;
    /** @var callable|null $previous_exception_handler */
    protected $previous_exception_handler = null;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        if (strlen(self::$reserved_memory) === 0) {
            self::$reserved_memory = str_repeat('x', 1024 * 10 /* KB */);
        }

        /** @link https://www.php.net/manual/function.set-error-handler.php */
        $this->previous_error_handler =
            set_error_handler(Closure::bind(Closure::fromCallable([$this, 'handleError']), $this));
        /** @link https://www.php.net/manual/function.set-exception-handler.php */
        $this->previous_exception_handler =
            set_exception_handler(Closure::bind(Closure::fromCallable([$this, 'handleException']), $this));
        /** @link https://www.php.net/manual/function.register-shutdown-function.php */
        register_shutdown_function(Closure::bind(Closure::fromCallable([$this, 'handleShutdown']), $this));
    }

    /**
     * Handles triggered or internal error
     *
     * @link https://www.php.net/manual/function.set-error-handler.php
     * @param integer $level
     * @param string $message
     * @param string $file
     * @param string $line
     * @return boolean
     */
    public function handleError(
        int $level,
        string $message,
        string $file = '',
        string $line = '0',
    ): bool {
        if (error_reporting() === 0) {
            // error was suppressed by @-operator
            return false;
        }

        if (in_array($level, [E_DEPRECATED, E_USER_DEPRECATED], true)) {
            $this->logger->warning(sprintf(
                '[DEPRECATED] %1$s in %2$s:%3$d',
                $message,
                $file,
                $line,
            ));
            // return back to logic
            return true;
        }

        if (error_reporting() & $level) {
            // raises ErrorException and will be catched by handleException
            throw new ErrorException($message, 0, $level, $file, (int)$line);
        }

        // uses php internal handler
        return false;
    }

    /**
     * Handles uncaught exception
     *
     * @param Throwable $exception
     * @return void
     */
    public function handleException(Throwable $exception): void
    {
        try {
            $this->noticeAboutIniDirective();
            $this->logger->error(sprintf(
                '[%1$s] %2$s',
                get_class($exception),
                $this->getErrorMessage($exception),
            ), compact('exception'));
        } catch (Throwable) {
            // ignore re-throwed exceptions
        }
    }

    /**
     * Handles shutdown function
     *
     * @return void
     */
    public function handleShutdown(): void
    {
        // free reserved memory for rendering exception
        self::$reserved_memory = '';

        set_error_handler($this->previous_error_handler);
        set_exception_handler($this->previous_exception_handler);
        $this->previous_error_handler = null;
        $this->previous_exception_handler = null;

        /** @var null|array{type:int, message:string, file: string, line: int} $last_error */
        $last_error = error_get_last();

        if ($last_error === null) {
            // No error
            return;
        }

        if (str_starts_with($last_error['message'], 'Allowed memory') || str_starts_with($last_error['message'], 'Out of memory')) {
            $this->handleException(new OutofMemoryError($last_error));
        } else {
            $this->handleException(new FatalError($last_error));
        }
    }

    /**
     * Get formatted error message
     *
     * @param Throwable $throwable
     * @return string
     */
    protected function getErrorMessage(Throwable $throwable): string
    {
        return match (true) {
            $throwable instanceof FatalError => 'Fatal: ' . $throwable->getMessage(),
            $throwable instanceof Error => 'Uncaught Error: ' . $throwable->getMessage(),
            $throwable instanceof ErrorException => 'Uncaught ' . $throwable->getMessage(),
            default => 'Uncaught Exception: ' . $throwable->getMessage(),
        };
    }

    /**
     * Framework says "display_errors" should be off.
     *
     * @return void
     */
    protected function noticeAboutIniDirective(): void
    {
        if (strtolower(ini_get('display_errors')) === 'on') {
            $this->logger->notice(
                'You should set the "display_errors" ini directive to "off".'
                . ' All errors will be caught by the framework and displayed properly.',
            );
        }
    }
}
