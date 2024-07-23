<?php

namespace App\Command;

use Elasticsearch\ClientBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'testElastic',
    description: 'tests connection with elasticsearch',
)]
class TestElasticsearchConnectionCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = ClientBuilder::create()->setHosts(['http://es01:9200'])->build();
        $response = $client->ping();

        if ($response) {
            $output->writeln('Connection to Elasticsearch successful!');
            return Command::SUCCESS;
        } else {
            $output->writeln('Failed to connect to Elasticsearch.');
            return Command::FAILURE;
        }
    }
}
