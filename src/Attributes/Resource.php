<?php namespace JSONAPI\Resource\Attributes;

/**
 * Attribute used to mark class as JSON:API resource.
 */
#[\Attribute(
    \Attribute::TARGET_CLASS
)]
class Resource
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        protected ?string $type = null,
        protected string $idFetcher = 'getId',
        protected array $options = []
    ) {}

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getIdFetcher(): string
    {
        return $this->idFetcher;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}