<?php

declare(strict_types=1);

namespace MyMicroService\Controller;

use League\Route\Http\Exception\BadRequestException;
use MyMicroService\Repository\ProductRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class ProductController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private ProductRepository $productRepository
    ) {
    }

    public function get(): array
    {
        return $this->productRepository->getAll();
    }

    public function getById(ServerRequestInterface $request, array $args): array
    {
        $this->logger->info('GET request received for ID ' . $args['id']);

        return $this->productRepository->get((int) $args['id']);
    }

    public function delete(ServerRequestInterface $request, array $args): array
    {
        $this->productRepository->delete((int) $args['id']);

        return [];
    }

    public function post(ServerRequestInterface $request): array
    {
        $parameters = json_decode($request->getBody()->getContents(), true);

        if (!isset($parameters['description'])) {
            throw new BadRequestException();
        }

        return [
            'id' =>  $this->productRepository->add($parameters['description'])
        ];
    }

    public function put(ServerRequestInterface $request, array $args): array
    {
        $parameters = json_decode($request->getBody()->getContents(), true);

        if (!isset($parameters['description'])) {
            throw new BadRequestException();
        }

        $this->productRepository->update((int) $args['id'], $parameters['description']);

        return [];
    }

}
