<?php

namespace App\Middleware;

use App\Command\CommandInterface;
use App\Command\LoadFixtures;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A middleware class that is being triggered when command is being executed. The middleware tries to find an associating class for the command that is being executed.
 */
class CliMiddleware
{
    private const COMMANDS = [
        'app:load-fixtures' => LoadFixtures::class
    ];
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (PHP_SAPI === 'cli') {
            $arguments = $_SERVER['argv'];
            $command = null;
            if (count($arguments) > 1) {
                $command = $arguments[1];
            }
            if (is_null($command) || !array_key_exists($command, self::COMMANDS)) {
                throw new Exception(sprintf('Command %s not found', $command));
            }

            $class = self::COMMANDS[$command];
            if (!class_exists($class)) {
                throw new Exception(sprintf('Command %s not found', $command));
            }

            if (!$this->container->has($class)) {
                throw new Exception(sprintf('Class %s is not registered as a dependency', $class));
            }

            $taskInstance = $this->container->get($class);
            if (!$taskInstance instanceof CommandInterface) {
                throw new Exception(sprintf('Class %s does not implement CommandInterface', $class));
            }
            $response = $taskInstance->execute($request, $response);

            echo 'success';
        }

        return $response;
    }
}