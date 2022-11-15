<?php

namespace App\Command;

use Exception;
use App\Entity\Upload;
use App\Service\Logger;
use App\Service\Uploader;
use App\Repository\UploadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Yauhenko\CronBundle\Attributes\Cron;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Output\OutputInterface;

#[Cron('app:clean -q', hourly: true)]
class CleanCommand extends Command {

    use LockableTrait;

    protected EntityManagerInterface $em;
    protected UploadRepository $uploadRepository;
    protected Uploader $uploader;

    public function __construct(EntityManagerInterface $em, UploadRepository $uploadRepository, Uploader $uploader) {
        parent::__construct();
        $this->em = $em;
        $this->uploadRepository = $uploadRepository;
        $this->uploader = $uploader;
    }

    protected function configure(): void {
        $this->setName('app:clean');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        if(!$this->lock(__FILE__)) {
            $io->warning('Locked by another process');
            return Command::FAILURE;
        }

        $conn = $this->em->getConnection();
        $freedUpSpace = 0;

        if(!$publicDir = realpath(__DIR__ . '/../../public')) {
            $io->error('Failed to detect public dir');
            return Command::FAILURE;
        }

        foreach($this->uploadRepository->getCleanPager() as $pagedData) {
            /** @var Upload $upload */
            foreach($pagedData->getData() as $upload) {
                $io->write('Processing: <fg=blue>' . $upload->getId() . '</> <fg=yellow>(' . $upload->getName() . ')</> ... ');
                try {
                    $conn->executeQuery('DELETE FROM upload WHERE id = :id', ['id' => $upload->getId()]);
                    unlink($publicDir . $upload->getPath());
                    $freedUpSpace += $upload->getSize();
                    $io->writeln('<fg=red>DELETED</>');
                } catch(Exception) {
                    $upload->touch();
                    $io->writeln('<fg=green>IN USE</>');
                }
            }
        }

        $this->em->flush();

        exec('find ' . $publicDir . ' -type d -empty -delete');

        $io->write('Deleting <fg=blue>expired logs</> ... ');
        $freedUpSpace += Logger::clean(7);
        $io->writeln('<fg=green>OK</>');

        $io->writeln('Freed up space: <fg=green>' . round($freedUpSpace / 1024 / 1024, 2) . ' Mb</>');
        $io->success('Done');

        return Command::SUCCESS;
    }

}
