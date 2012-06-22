<?php

namespace Nass600\CosmBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Nass600\CosmBundle\Model\ConnectionManager;

/**
 * ReadFeedCommand
 *
 * @package Nass600CosmBundle
 * @subpackage Command
 * @author Ignacio Velazquez <ivelazquez85@gmail.com>
 */
class ReadFeedCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cosm:feed:read')
            ->setDescription('Reads the given feed')
            ->setDefinition(array(
                new InputArgument(
                    'feedId', InputArgument::REQUIRED, 'Feed id.'
                ),
                new InputArgument(
                    'apiKey', InputArgument::REQUIRED, 'API Key.'
                )
            ))
            ->addOption('dump', null, InputOption::VALUE_NONE, 'Dumps to standard output the complete message structure')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiKey = $input->getArgument('apiKey');
        $feedId = $input->getArgument('feedId');

        $data = $this->getContainer()->get('cosm.feed_manager')->readFeed($feedId, $apiKey);

//        if (!empty($data->errors)){
//            foreach ($data->errors as $error)
//                $output->writeln('<error>'.$error.'</error>');
//        } else{
//            $date = new \DateTime($data->at);
////            var_dump($this->isValidTimestampInterval($date));die;
//            if ($input->getOption('dump')) {
//                $output->writeln(var_dump($data));
//            }
//            else
//                $output->writeln('<info>'.$date->format('Y-m-d H:i:s') .'</info> > <comment>'. $data->current_value . ' W</comment>');
//        }
    }

    /**
     * Checks timestamp interval among requests
     *
     * @param \DateTime $date
     * @return bool
     */
    protected function isValidTimestampInterval(\DateTime $date)
    {
        $timestamp = $date->getTimestamp();
        $now = new \DateTime('now');
        $now_timestamp = $now->getTimestamp();
//        var_dump($date->format('Y-m-d H:i:s'));
//        var_dump($now->format('Y-m-d H:i:s'));
        return !(($now_timestamp - $timestamp) > 300);
    }
}
