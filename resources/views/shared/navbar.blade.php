<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('home') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img class="w-75 m-0" src="/assets/img/logos/logo.svg" alt="">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ active('dashboard.admin') }}">
            <a href="{{ route('dashboard.admin') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <!-- /Dashboard -->

        <!-- ConfiguracionesGenerales -->
        <li style="" class="menu-item {{ active(['ciclos.*','semestres.*','materias.*','profesores.*', 'grupos.*', 'alumnos.*', 'horarios.*'], 'active open') }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='menu-icon bx bx-list-ul'></i>
                <div>Operaciones</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item 
                {{ active('ciclos.*') }}">
                    <a href="{{ route('ciclos.index') }}" class="menu-link">
                        <div>Ciclos</div>
                    </a>
                </li>
                <li class="menu-item {{ active('semestres.*') }}">
                    <a href="{{ route('semestres.index')}}" class="menu-link">
                        <div>Semestres</div>
                    </a>
                </li>
                <li class="menu-item {{ active('materias.*') }}">
                    <a href="{{ route('materias.index')}}" class="menu-link">
                        <div>Materias</div>
                    </a>
                </li>
                <li class="menu-item {{ active(['profesores.*', 'horarios.*']) }}">
                    <a href="{{ route('profesores.index')}}" class="menu-link">
                        <div>Profesores</div>
                    </a>
                </li>
                <li class="menu-item {{ active('alumnos.*') }}">
                    <a href="{{ route('alumnos.index')}}" class="menu-link">
                        <div>Alumnos</div>
                    </a>
                </li>
                <li class="menu-item {{ active('grupos.*') }}">
                    <a href="{{ route('grupos.index')}}" class="menu-link">
                        <div>Grupos</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- /ConfiguracionesGenerales -->
    </ul>
</aside>
<!-- / Menu -->
