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
            ->setDescription('Adds a new image to the database with a reference to its location on S3.')
            ->setHelp('This command allows you to add images to the database specifying the image type such as profile or gallery.')
            ->addArgument('filename', InputArgument::REQUIRED, 'The filename of the image')
            ->addArgument('url', InputArgument::REQUIRED, 'The url of an image')
            ->addArgument('objectKey', InputArgument::REQUIRED, 'ObjectKey - path to the image on S3')
            ->addArgument('type', InputArgument::REQUIRED, 'The type of the image (profile or gallery)')
            ->addArgument('description', InputArgument::OPTIONAL, 'The description on the image')
            ->addArgument('showOnHomepage', InputArgument::OPTIONAL, 'Will the image be displayed on the homepage?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $image = new Image();
        $image->setFilename($input->getArgument('filename'));
        $image->setUrl($input->getArgument('url'));
        $image->setObjectKey($input->getArgument('objectKey'));
        $image->setType($input->getArgument('type'));
        $image->setDescription($input->getArgument('description') ?? 'No description provided');
        $image->setShowOnHomepage((bool)$input->getArgument('showOnHomepage'));
        $image->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        $output->writeln("Image added successfully with type: " . $image->getType());

        return Command::SUCCESS;
    }
}
