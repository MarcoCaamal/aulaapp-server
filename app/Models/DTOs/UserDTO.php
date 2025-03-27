<?php
namespace App\Models\DTOs;

class UserDTO {
    public int $id;
    public string $nombre;
    public string $curp;
    public ?array $roles = null;

}