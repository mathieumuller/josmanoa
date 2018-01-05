<?php

namespace App\Command;

use App\Entity\Media;
use App\Manager\MediaManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TransferMediaCommand extends Command
{
    private $em;
    private $mediaManager;

    public function __construct(EntityManagerInterface $em, MediaManager $mediaManager)
    {
        $this->em = $em;
        $this->mediaManager = $mediaManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:transfer-media')
            ->setDescription('Transfer local media to s3 bucket')
            ->setHelp('This command allows you to transfer local media to s3 bucket...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $medias = $this->em->getRepository('App:Media')->findBy(['status' => Media::STATUS_LOCAL], ['createdAt' => 'ASC']);

        $this->lockMedias($medias);

        foreach ($medias as $media) {
            $this->mediaManager->transfer($media);
        }

        $this->em->flush();
    }

    private function lockMedias($medias)
    {
        foreach ($medias as $media) {
            $media->setStatus(Media::STATUS_TRANSFER_PROCESSING);
        }

        $this->em->flush();
    }
}
