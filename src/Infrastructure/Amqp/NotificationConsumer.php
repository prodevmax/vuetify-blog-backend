<?php
namespace App\Infrastructure\Amqp;


use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Output\ConsoleOutput;

class NotificationConsumer implements ConsumerInterface
{
    /**
     * @param AMQPMessage $msg
     * @return mixed|void
     */
    public function execute(AMQPMessage $msg)
    {
        $output = new ConsoleOutput();
        $output->writeln('<comment>Consume messages</comment>');
        $output->writeln(print_r(json_decode($msg->getBody(), true), true));
        $output->writeln('<info>Done!</info>');
    }
}