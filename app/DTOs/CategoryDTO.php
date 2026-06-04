<?php

namespace App\DTOs;

class CategoryDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $description = null,
        public readonly string $color = '#3b82f6',
        public readonly bool $isActive = true,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            type: $data['type'],
            description: $data['description'] ?? null,
            color: $data['color'] ?? '#3b82f6',
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'type' => $this->type,
            'is_active' => $this->isActive,
        ];
    }
}
