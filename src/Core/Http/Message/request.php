<?php
namespace Fenrir\Core\Http\Message;

use InvalidArgumentException;

class Request extends Message implements RequestInterface
{
    protected $method;
    protected $requestTarget;
    protected $uri;

    public function __construct(string $method, $uri, string $protocolVersion = '1.1')
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->protocol = $protocolVersion;
    }

    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();
        if ($target === '') {
            $target = '/';
        }

        if ($this->uri->getQuery() !== '') {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    public function withRequestTarget($requestTarget): static
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
        }

        $copy = clone $this;
        $copy->requestTarget = $requestTarget;
        return $copy;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod($method): static
    {
        $copy = clone $this;
        $copy->method = $method;
        return $copy;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false): static
    {
        $copy = clone $this;
        $copy->uri = $uri;

        if (!$preserveHost) {
            if ($uri->getHost() !== '') {
                $copy->headers['Host'] = [$uri->getHost()];

                if ($uri->getPort() !== null) {
                    $copy->headers['Host'][] = $uri->getPort();
                }
            }
        }

        return $copy;
    }
}
