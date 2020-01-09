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

namespace RQuadlingTests\Controller\Fixtures\Commands;

use RQuadling\Console\Abstracts\AbstractCommand;
use RQuadlingTests\Controller\Fixtures\Commands\Namespaced\NamespacedTestCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends AbstractCommand
{
    /**
     * @var NamespacedTestCommand
     * @DelayedInject
     */
    public $namespacedTestCommand;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $this->output->writeln(\var_export($this->input->getArguments(), true));
        $this->output->writeln(\var_export($this->input->getOptions(), true));

        return $this->input->getOption('exit-code');
    }

    protected function configure($commandName = null)
    {
        parent::configure($commandName);

        $this
            ->setDescription('Test Command')
            ->addArgument(
                'optional-argument',
                InputArgument::OPTIONAL,
                'The first argument is optional'
            )
            ->addArgument(
                'many-optional-argument',
                InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'The rest of the arguments are optional too'
            )
            ->addOption(
                'long-with-short-option-no-default',
                'l',
                InputOption::VALUE_OPTIONAL,
                'A long option with a short option with an optional value - no default'
            )
            ->addOption(
                'long-option-no-default',
                null,
                InputOption::VALUE_OPTIONAL,
                'A long option only with an optional value - no default'
            )->addOption(
                'long-with-short-option-default',
                's',
                InputOption::VALUE_OPTIONAL,
                'A long option with a short option with an optional value with default',
                'default'
            )
            ->addOption(
                'long-option-default',
                null,
                InputOption::VALUE_OPTIONAL,
                'A long option only with an optional value with default',
                'default'
            )
            ->addOption(
                'exit-code',
                null,
                InputOption::VALUE_OPTIONAL,
                'What exit code is required?',
                0
            )
            ->addOption(
                'multiple-options',
                'm',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Multiple options with values'
            );
    }
}
