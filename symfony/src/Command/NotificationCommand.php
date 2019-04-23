<?php

namespace App\Command;

use App\Entity\Post;
use App\Entity\Subscribe;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\SubscribeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;
use Swift_Mailer;
use Swift_Message;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class NotificationCommand extends Command
{
    protected static $defaultName = 'app:notification';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $templating;

    public function __construct(EntityManagerInterface $entityManager, Swift_Mailer $mailer, Environment $templating)
    {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->templating = $templating;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Notify users about posts')
            ->setHelp('This command allows you to send notification to users about posts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var User[] $users */
        $users = $this->entityManager->getRepository(User::class)->findAll();

        /** @var PostRepository $repository */
        $repository = $this->entityManager->getRepository(Post::class);

        foreach ($users as $user) {
            $subsribes = $user->getSubscribes();

            if (count($subsribes) !== 0) {
                $categories = [];

                foreach ($subsribes as $subsribe) {
                    $categories[] = $subsribe->getCategory();
                }

                $posts = $repository
                    ->findRecomendedQueryBuilder($categories, Post::NOTIFICATION_QUANTITY_PER_PAGE)
                    ->getQuery()
                    ->getResult()
                ;

                if (count($posts) !== 0) {
                    try {
                        $this->send($user, $posts);
                    } catch (LoaderError $error) {
                    } catch (RuntimeError $error) {
                    } catch (SyntaxError $error) {
                    }
                }
            }
        }
    }

    /**
     * @param User $user
     * @param array $posts
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function send(User $user, array $posts)
    {
        $message = (new Swift_Message('Super Blog 2'))
            ->setFrom('yamadote@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'emails/notification.html.twig',
                    ['posts' => $posts]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
