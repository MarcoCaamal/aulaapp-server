<?php

namespace App\Providers;

use App\Services\Foros\RespuestaService;
use App\Services\Interfaces\Foros\RespuestaServiceInterface;
use Laravel\Sanctum\Sanctum;
use App\Services\UserService;
use App\Services\CicloService;
use App\Services\GrupoService;
use App\Services\HorarioService;
use App\Services\MateriaService;
use App\Services\AsesoriaService;
use App\Services\SemestreService;
use App\Models\PersonalAccessToken;
use App\Services\Foros\ForoService;
use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\Interfaces\CicloServiceInterface;
use App\Services\Interfaces\GrupoServiceInterface;
use App\Services\Interfaces\HorarioServiceInterface;
use App\Services\Interfaces\MateriaServiceInterface;
use App\Services\Interfaces\AsesoriaServiceInterface;
use App\Services\Interfaces\Foros\ForoServiceInterface;
use App\Services\Interfaces\Mapping\MapperServiceInterface;
use App\Services\Interfaces\SemestreServiceInterface;
use App\Services\Mapping\MapperService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CicloServiceInterface::class, CicloService::class);
        $this->app->bind(SemestreServiceInterface::class, SemestreService::class);
        $this->app->bind(MateriaServiceInterface::class, MateriaService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(GrupoServiceInterface::class, GrupoService::class);
        $this->app->bind(HorarioServiceInterface::class, HorarioService::class);
        $this->app->bind(AsesoriaServiceInterface::class, AsesoriaService::class);
        $this->app->bind(MapperServiceInterface::class, MapperService::class);
        $this->app->bind(ForoServiceInterface::class, ForoService::class);
        $this->app->bind(RespuestaServiceInterface::class, RespuestaService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Pone a eloquent en modo estricto
        // Model::shouldBeStrict(
        //     !app()->isProduction() // Solo en modo desarrollo
        // );
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
