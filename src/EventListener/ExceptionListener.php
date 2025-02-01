<?php

namespace App\EventListener;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;


#[AsEventListener]
class ExceptionListener
{
  private LoggerInterface $logger;

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public function __invoke(ExceptionEvent $event): void
  {
    $this->logger->info("A new exception was invoked!");
    $exception = $event->getThrowable();

    $response = new Response();
    $response->setContent("Oopsiee: " . $exception->getMessage());

    $response->headers->set('Content-Type', 'text/html');
    $response->setStatusCode(500);
    $event->setResponse($response);
  }
}