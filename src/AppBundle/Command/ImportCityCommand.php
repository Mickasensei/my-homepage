<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCityCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('import:cities')
            ->setDescription('Import Cities Command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configDirectories = array(__DIR__.'/config');
        $locator = new FileLocator($configDirectories);
        $citiesFile = $locator->locate('cities.json', null, true);
        $handle = fopen($citiesFile, "r");

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        if ($handle) {
            $i = 1;
            while (($line = fgets($handle)) !== false) {
                $i++;
                $tabCity = json_decode(str_replace('\n','',$line), true);
                $city = new City();
                $city->setIdApi($tabCity['_id']);
                $city->setName($tabCity['name']);
                $em->persist($city);
                if ($i%1000 == 0) {
                    $em->flush();
                    $em->clear();
                    gc_collect_cycles();
                }
            }
            $em->flush();
            $em->clear();
            gc_collect_cycles();
            fclose($handle);
            echo "Fin de l'import";
        } else {
            echo 'File not found';
        }
    }
}
