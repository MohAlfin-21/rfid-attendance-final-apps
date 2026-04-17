<?php

namespace App\Domain\Attendance\DTOs;

use App\Models\Classroom;
use App\Models\User;

readonly class StudentIdentityData
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $username = null,
        public ?string $classroomName = null,
    ) {}

    public static function fromUser(User $user, ?Classroom $classroom = null): self
    {
        return new self(
            id: (int) $user->getKey(),
            name: $user->name,
            username: $user->username,
            classroomName: $classroom?->name,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'classroom' => $this->classroomName,
        ];
    }
}
