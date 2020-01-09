<?php

/**
 * RQuadling/ApplicationController
 *
 * LICENSE
 *
 * This is free and unencumbered software released into the public domain.
 *
 * Anyone is free to copy, modify, publish, use, compile, sell, or distribute this software, either in source code form or
 * as a compiled binary, for any purpose, commercial or non-commercial, and by any means.
 *
 * In jurisdictions that recognize copyright laws, the author or authors of this software dedicate any and all copyright
 * interest in the software to the public domain. We make this dedication for the benefit of the public at large and to the
 * detriment of our heirs and successors. We intend this dedication to be an overt act of relinquishment in perpetuity of
 * all present and future rights to this software under copyright law.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT
 * OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * For more information, please refer to <https://unlicense.org>
 *
 */

namespace RQuadling\Controller;

use Exception;
use GuzzleHttp\Psr7\ServerRequest;
use RQuadling\Console\Input\Input;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use SensioLabs\AnsiConverter\Theme\SolarizedXTermTheme;
use Symfony\Component\Console\Output\BufferedOutput;
use Throwable;

class ApplicationController
{
    /**
     * The Symfony Console application (based upon RQuadling\Console\Abstracts\AbstractApplication) that we want to
     * provide a web interface to.
     *
     * @var ApplicationInterface
     * @Inject
     */
    protected $application;

    /**
     * Specify a particular output type.
     *
     * @var BufferedOutput
     * @Inject
     */
    protected $output;

    /**
     * Our slightly less restrictive Input.
     *
     * @var Input
     * @Inject
     */
    protected $input;

    /** @var AnsiToHtmlConverter */
    private $converter;

    /**
     * Interface with ingress utilities.
     *
     * Excludes 'help' and 'list' default commands.
     *
     * Supports the following option patterns
     * 1. ?long-option                           => --long-option
     * 2. ?long-option=with-value                => --long-option with-value
     * 3. ?a                                     => -a (single short option)
     * 4. ?a=with-value                          => -a with-value (single short option with value)
     * 5. ?long-option=value1&long-option=value2 => --long-option value1 --long-option value2
     * 6. ?a=value1&a=value2                     => -a value1 -a value2 (multiple short options with different values)
     * 7. ?v, ?vv, ?vvv                          => -v, -vv, -vvv (special case for verbosity)
     *
     * Cannot support
     * 1. ?abc (multiple short options aggregated into a single argument)
     *    This must be presented as ?a&b&c rather than ?abc
     * 2. Long or short options with non unique values (i.e. -a=1 -a=1 will result in a single -a=1).
     *
     * @throws Exception
     */
    public function index(): string
    {
        // We will handle the application exit.
        $this->application->setAutoExit(false);

        // We will handle the exception catching.
        $this->application->setCatchExceptions(false);

        $uriPath = $this->getUriPath();

        // Set up the command line arguments.
        $arguments = \array_filter(\explode('/', $uriPath));

        // Do not allow any interactions.
        $arguments[] = '--no-interaction';

        // Add in remaining parameters.
        $queryParams = $this->getQueryParams();
        \array_map(
            function ($value, $key) use (&$arguments) {
                switch ($key) {
                    // --no-interaction is already enforced.
                    case 'no-interaction':
                        break;

                    // Verbosity
                    case 'v':
                    case 'vv':
                    case 'vvv':
                        $arguments[] = \sprintf('-%s', $key);
                        break;

                    // Everything else
                    default:
                        // Support multiple unique values for a param.
                        $prefixedKey = \sprintf('%s-%s', \strlen($key) == 1 ? '' : '-', $key);
                        if ($value) {
                            $value = (array)$value;
                            foreach ($value as $singleValue) {
                                $arguments[] = \sprintf(
                                    '%s%s%s',
                                    $prefixedKey,
                                    \strlen($key) == 1 ? '' : '=',
                                    $singleValue
                                );
                            }
                        } else {
                            $arguments[] = $prefixedKey;
                        }
                }
            },
            $queryParams,
            \array_keys($queryParams)
        );

        // Add ansi unless no-ansi is provided.
        if (!\in_array('--no-ansi', $arguments)) {
            $arguments[] = '--ansi';
        }

        $this->input->setTokens($arguments);

        // Run the command, get the output and catch any errors.
        try {
            $this->application->run($this->input, $this->output);
            $output = $this->output->fetch();
        } catch (Throwable $ex) {
            $output = $ex->getMessage();
        }

        // Render ansi as HTML if required.
        if ($this->output->isDecorated()) {
            $this->converter = new AnsiToHtmlConverter(new SolarizedXTermTheme(), false);
            $css = $this->converter->getTheme()->asCss();
            $output = $this->converter->convert($output);
        } else {
            $css = '';
        }

        return <<< END_TEMPLATE
<html>
<head>
    <title>Ingress Utilities</title>
    <style>
        body {
            background-color: #262626;
            color: #e4e4e4;
        }

        pre {
            overflow: auto;
            padding: 10px 15px;
            font-family: monospace;
        }

        {$css}
    </style>
</head>
<body>
<pre>{$output}</pre>
</body>
</html>
END_TEMPLATE;
    }

    protected function getUriPath(): string
    {
        return ServerRequest::fromGlobals()->getUri()->getPath();
    }

    protected function getQueryParams(): array
    {
        return ServerRequest::fromGlobals()->getQueryParams();
    }
}
