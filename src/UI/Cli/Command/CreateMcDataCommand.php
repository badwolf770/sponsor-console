<?php

declare(strict_types=1);

namespace UI\Cli\Command;
use App\Shared\Infrastructure\Bus\AsyncEvent\MessengerAsyncEventBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMcDataCommand extends Command
{
    private MessengerAsyncEventBus $asyncEventBus;

    public function __construct(MessengerAsyncEventBus $asyncEventBus)
    {
        parent::__construct();

        $this->asyncEventBus = $asyncEventBus;
    }

    protected function configure(): void
    {
        $this
            ->setName('queue:create-mc-data')
            ->setDescription('Given a uuid and email, generates a new user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>User Created: </info>');
        $output->writeln('');

        return 1;
    }
}