<?php
namespace App\Models\DTOs\Paginacion;

use App\Models\DTOs\Operaciones\Personas\ProfesorDTO;
use OpenApi\Attributes as OAT;

#[OAT\Schema(
    title: 'Paginador'
)]
class PaginadorDTO
{
    #[OAT\Property(
        description: 'Página actual',
        title: 'Pagina Actual',
        example: 1
    )]
    public int $current_page;
    #[OAT\Property(
        description: 'Datos que se obtenieron',
        title: 'Datos',
        items: new OAT\Items(
            oneOf: [
                new OAT\Schema(
                    ref: ProfesorDTO::class
                )
            ]
        )
    )]
    public array $data;
    #[OAT\Property(
        description: 'Url de la primera página',
        title: 'URL '
    )]
    public string $first_page_url;
    #[OAT\Property()]
    public int $from;
    #[OAT\Property()]
    public int $last_page;
    #[OAT\Property()]
    public string $last_page_url;
    #[OAT\Property(
        items: new OAT\Items(
            ref: LinkPaginador::class
        )
    )]
    public array $links;
    #[OAT\Property()]
    public string $next_page_url;
    #[OAT\Property()]
    public string $path;
    #[OAT\Property()]
    public int $per_page;
    #[OAT\Property()]
    public string $prev_page_url;
    #[OAT\Property()]
    public int $to;
    #[OAT\Property()]
    public int $total;

}

#[OAT\Schema(
    title: 'LinkPaginador'
)]
class LinkPaginador
{
    #[OAT\Property()]
    public string $url;
    #[OAT\Property()]
    public string $label;
    #[OAT\Property()]
    public bool $active;
}