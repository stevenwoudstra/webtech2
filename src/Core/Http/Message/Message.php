<?php

namespace Fenrir\Core\Http\Message;

abstract class Message implements MessageInterface {

    protected $protocol = '1.1';
    protected $headers = [];
    protected $normelizedHeaderNames = [string, string];


    public function getProtocolVersion(): string 
    {
        return $this->protocol;
    }

    public function withProtocolVersion(string $version): static 
    {
        $copy = clone $this;
        $copy->getProtocolVersion() = $version;
        return $copy;
    }

    private function normalizeHeaders(): void 
    {
        if (empty($this->normelizedHeaderNames)) {
            foreach ($this->headers as $name => $values) {
                $this->normelizedHeaderNames[strtolower($name)] = $name;
            }
        }
    }

    private function normalizeGetHeaderName(string $name): string 
    {
        $this->normalizeHeaders();
        return $this->normelizedHeaderNames[strtolower($name)];
    }

    public function getHeaders(): array 
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool 
    {
        $this->normalizeHeaders();
        return isset($this->normelizedHeaderNames[strtolower($name)]);
    }

    public function getHeader(string $name): array 
    {
        $this->normalizeHeaders();
        return $this->headers[$this->normalizeGetHeaderName($name)] ?? [];
    }

    public function getHeaderLine(string $name): string 
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, string | array $value): static 
    {
        if ($this->hasHeader($name)) {
            throw new \InvalidArgumentException("Header $name does not exist");
        }
        $copy = clone $this;
        $copy->headers[$copy->normalizeGetHeaderName($name)] = $value;
        return $copy;
    }

    public function withAddedHeader(string $name, string | array $value): static 
    {
        
        if ($this->hasHeader($name)) {
            throw new \InvalidArgumentException("Header $name does not exist");
        }
        $copy = clone $this;
        array_push($copy->headers[$copy->normalizeGetHeaderName($name)], $value);
        return $copy;
    }

    public function withoutHeader(string $name): static 
    {
        $copy = clone $this;
        unset($copy->headers[$copy->normalizeGetHeaderName($name)]);
        return $copy;
    }

    //streams not supported yet
 


}
