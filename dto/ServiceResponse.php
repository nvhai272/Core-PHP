<?php
class ServiceResponse
{
    private bool $success;
    private array $errors;
    private mixed $data;

    public function __construct(bool $success, array $errors = [], mixed $data = null)
    {
        $this->success = $success;
        $this->errors  = $errors;
        $this->data    = $data;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
