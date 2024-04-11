<?php

namespace App\Command;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'AddImageCommand',
    description: 'Add images to db',
)]
class AddImageCommand extends Command
{
    private EntityManagerInterface $entityManager;
    protected static $defaultName = 'Gallery:add-image';
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Adds a new image.')
            ->setHelp('This command lets you add images to the database (reference to S3).')
            ->addArgument('filename', InputArgument::REQUIRED, 'The filename of the image')
            ->addArgument('url', InputArgument::REQUIRED, 'The url of an image')
            ->addArgument('objectKey', InputArgument::REQUIRED, 'ObjectKey - path to the image on S3')
            ->addArgument('description', InputArgument::OPTIONAL, 'The description on the image')
            ->addArgument('showOnHomepage', InputArgument::OPTIONAL, 'Will the image be displayed on the homepage?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $image = new Image();
        $image->setUrl($input->getArgument('url'));
        $image->setFilename($input->getArgument('filename'));
        $image->setDescription($input->getArgument('description'));
        $image->setShowOnHomepage($input->getArgument('showOnHomepage'));
        $image->setObjectKey($input->getArgument('objectKey'));
        $image->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $output->writeln("Image added!");

        return Command::SUCCESS;
    }
}
