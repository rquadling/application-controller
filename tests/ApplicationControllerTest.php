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

namespace RQuadlingTests\Controller;

use Exception;
use josegonzalez\Dotenv\Loader;
use PHPUnit\Framework\TestCase;
use RQuadling\Controller\ApplicationController;
use RQuadling\DependencyInjection\ContainerFactory;

class ApplicationControllerTest extends TestCase
{
    /** @var ApplicationController */
    private static $applicationController;

    public static function setUpBeforeClass()
    {
        (new Loader(__DIR__.'/Fixtures/Commands/.env'))->parse()->toEnv(true);

        /** @var ApplicationController $applicationController */
        static::$applicationController = ContainerFactory::build()->make(ApplicationController::class);
    }

    /**
     * @dataProvider providerForApplicationController
     *
     * @throws Exception
     */
    public function testApplicationControllerProcessesGetSuperGlobal($uri, array $get, string $errorMessage = null)
    {
        $this->assertInstanceOf(ApplicationController::class, static::$applicationController);

        $_GET = $get;
        $_SERVER['REQUEST_URI'] = $uri;

        // Have to sanitize the output for code coverage as full paths are output to PHPUnit, rather than relative paths.
        $output = \str_replace(
            [
                \dirname(__DIR__),
                'vendor/phpunit/phpunit/phpunit',
                './vendor',
            ],
            [
                '.',
                'vendor/bin/phpunit',
                'vendor',
            ],
            static::$applicationController->index()
        );
        $comparisonFilename = \sprintf(
            '%s/data/%s.html',
            __DIR__,
            \trim($this->getDataSetAsString(false))
        );

        $this->assertEquals(\file_get_contents($comparisonFilename), $output);
    }

    public function providerForApplicationController()
    {
        return [
            'abc with no-ansi' => [
                null,
                [
                    'a' => null,
                    'b' => null,
                    'c' => null,
                    'no-ansi' => null,
                ],
                'The &quot;-a&quot; option does not exist.',
            ],
            'abc' => [
                null,
                [
                    'a' => null,
                    'b' => null,
                    'c' => null,
                ],
                'The &quot;-a&quot; option does not exist.',
            ],
            'abcd with no-ansi' => [
                null,
                [
                    'abcd' => null,
                    'no-ansi' => null,
                ],
                'The &quot;--abce&quot; option does not exist.',
            ],
            'abcd' => [
                null,
                [
                    'abcd' => null,
                ],
                'The &quot;--abce&quot; option does not exist.',
            ],
            'ansi' => [
                null,
                [
                    'ansi' => null,
                ],
            ],
            'Empty array' => [
                null,
                [
                ],
            ],
            'h with no-ansi' => [
                null,
                [
                    'h' => null,
                    'no-ansi' => null,
                ],
            ],
            'h' => [
                null,
                [
                    'h' => null,
                ],
            ],
            'help with no-ansi' => [
                null,
                [
                    'help' => null,
                    'no-ansi' => null,
                ],
            ],
            'help' => [
                null,
                [
                    'help' => null,
                ],
            ],
            'list with xml' => [
                'list',
                [
                    'format' => 'xml',
                ],
            ],
            'list' => [
                'list',
                [
                ],
            ],
            'list with xml and no ansi' => [
                'list',
                [
                    'format' => 'xml',
                    'no-ansi' => null,
                ],
            ],
            'list with no ansi' => [
                'list',
                [
                    'no-ansi' => null,
                ],
            ],
            'no-ansi' => [
                null,
                [
                    'no-ansi' => null,
                ],
            ],
            'no-interaction' => [
                null,
                [
                    'no-interaction' => null,
                ],
            ],
            'test-command exit-code 1 with no-ansi' => [
                'test-command',
                [
                    'no-ansi' => null,
                    'exit-code' => 1,
                ],
            ],
            'test-command exit-code 1' => [
                'test-command',
                [
                    'exit-code' => 1,
                ],
            ],
            'test-command with 1 argument with no-ansi' => [
                'test-command/arg1',
                [
                    'no-ansi' => null,
                ],
            ],
            'test-command with 1 argument' => [
                'test-command/arg1',
                [
                ],
            ],
            'test-command with 5 argument with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                ],
            ],
            'test-command with 5 argument' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                ],
            ],
            'test-command with 5 argument, long and short options with no values with no-ansi and exit code' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => null,
                    'long-option-no-default' => null,
                    'exit-code' => 1,
                ],
            ],
            'test-command with 5 argument, long and short options with no values with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => null,
                    'long-option-no-default' => null,
                ],
            ],
            'test-command with 5 argument, long and short options with values with no-ansi and exit code' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => 'val1',
                    'long-option-no-default' => 'val2',
                    'exit-code' => 1,
                ],
            ],
            'test-command with 5 argument, long and short options with values with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => 'val1',
                    'long-option-no-default' => 'val2',
                ],
            ],
            'test-command with 5 argument, m options, long and short options with no values with no-ansi and exit code' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => null,
                    'long-option-no-default' => null,
                    'exit-code' => 1,
                    'm' => [
                        1,
                        2,
                        3,
                        4,
                        5,
                    ],
                ],
            ],
            'test-command with 5 argument, m options, long and short options with no values with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => null,
                    'long-option-no-default' => null,
                    'm' => [
                        1,
                        2,
                        3,
                        4,
                        5,
                    ],
                ],
            ],
            'test-command with 5 argument, m options, long and short options with values with no-ansi and exit code' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => 'val1',
                    'long-option-no-default' => 'val2',
                    'exit-code' => 1,
                    'm' => [
                        1,
                        2,
                        3,
                        4,
                        5,
                    ],
                ],
            ],
            'test-command with 5 argument, m options, long and short options with values with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => 'val1',
                    'long-option-no-default' => 'val2',
                    'm' => [
                        1,
                        2,
                        3,
                        4,
                        5,
                    ],
                ],
            ],
            'test-command with 5 argument, mixed options, long and short options with no values with no-ansi and exit code' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => null,
                    'long-option-no-default' => null,
                    'exit-code' => 1,
                    'm' => [
                        1,
                        3,
                        5,
                    ],
                    'multiple-options' => [
                        2,
                        4,
                    ],
                ],
            ],
            'test-command with 5 argument, mixed options, long and short options with no values with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => null,
                    'long-option-no-default' => null,
                    'm' => [
                        1,
                        3,
                        5,
                    ],
                    'multiple-options' => [
                        2,
                        4,
                    ],
                ],
            ],
            'test-command with 5 argument, mixed options, long and short options with values with no-ansi and exit code' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => 'val1',
                    'long-option-no-default' => 'val2',
                    'exit-code' => 1,
                    'm' => [
                        1,
                        3,
                        5,
                    ],
                    'multiple-options' => [
                        2,
                        4,
                    ],
                ],
            ],
            'test-command with 5 argument, mixed options, long and short options with values with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => 'val1',
                    'long-option-no-default' => 'val2',
                    'm' => [
                        1,
                        3,
                        5,
                    ],
                    'multiple-options' => [
                        2,
                        4,
                    ],
                ],
            ],
            'test-command with 5 argument, multiple options, long and short options with no values with no-ansi and exit code' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => null,
                    'long-option-no-default' => null,
                    'exit-code' => 1,
                    'multiple-options' => [
                        1,
                        2,
                        3,
                        4,
                        5,
                    ],
                ],
            ],
            'test-command with 5 argument, multiple options, long and short options with no values with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => null,
                    'long-option-no-default' => null,
                    'multiple-options' => [
                        1,
                        2,
                        3,
                        4,
                        5,
                    ],
                ],
            ],
            'test-command with 5 argument, multiple options, long and short options with values with no-ansi and exit code' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => 'val1',
                    'long-option-no-default' => 'val2',
                    'exit-code' => 1,
                    'multiple-options' => [
                        1,
                        2,
                        3,
                        4,
                        5,
                    ],
                ],
            ],
            'test-command with 5 argument, multiple options, long and short options with values with no-ansi' => [
                'test-command/arg1/arg2/arg3/arg4/arg5',
                [
                    'no-ansi' => null,
                    'l' => 'val1',
                    'long-option-no-default' => 'val2',
                    'multiple-options' => [
                        1,
                        2,
                        3,
                        4,
                        5,
                    ],
                ],
            ],
            'test-command with no-ansi' => [
                'test-command',
                [
                    'no-ansi' => null,
                ],
            ],
            'test-command' => [
                'test-command',
                [
                ],
            ],
            'v' => [
                null,
                [
                    'v' => null,
                ],
            ],
            'vv' => [
                null,
                [
                    'vv' => null,
                ],
            ],
            'vvv' => [
                null,
                [
                    'vvv' => null,
                ],
            ],
        ];
    }
}
