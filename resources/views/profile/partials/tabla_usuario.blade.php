<div class="table-responsive">
    <table class="table table-borderless align-middle tabla-pro">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th class="text-center">Estatus</th>
                <th class="text-center">Acción</th>
            </tr>
        </thead>

        <tbody>
        @forelse($usuarios as $usuario)
            <tr>
                <td class="fw-semibold text-primary-soft">
                    {{ $usuario->email }}
                </td>
                <td>{{ $usuario->name }}</td>
                <td>
                    <span class="badge bg-light text-dark border px-3">
                        {{ $usuario->rol }}
                    </span>
                </td>

                <td class="text-center">
                    <span id="badge-{{ $usuario->id }}"
                          class="badge estado-pill {{ $usuario->activo ? 'activo':'inactivo' }}">
                        <i class="bi {{ $usuario->activo ? 'bi-check-circle':'bi-x-circle' }} me-1"></i>
                        {{ $usuario->activo ? 'Activo':'Inactivo' }}
                    </span>
                </td>

                <td class="text-center">
                    <label class="switch">
                        <input type="checkbox"
                               class="toggle-estado-usuarios"
                               data-id="{{ $usuario->id }}"
                               data-url="{{ route('usuarios.toggle',$usuario) }}"
                               {{ $usuario->activo ? 'checked':'' }}>
                        <span class="slider"></span>
                    </label>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-people fs-4 d-block mb-2"></i>
                    No se encontraron usuarios
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-end mt-3">
        {{ $usuarios->links('pagination::bootstrap-5') }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root {
    --primary: #0d6efd;
    --success: #198754;
    --danger: #dc3545;
    --soft-bg: #f8f9fa;
}

/* TABLA GENERAL */
.tabla-pro {
    border-collapse: separate;
    border-spacing: 0 12px;
}

/* HEADER */
.tabla-pro thead th {
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #6c757d;
    border: none;
    padding-bottom: .75rem;
}

/* FILAS */
.tabla-pro tbody tr {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
    transition: all .25s ease;
}

.tabla-pro tbody tr:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 30px rgba(0,0,0,.12);
}

/* CELDAS */
.tabla-pro td {
    border: none;
    padding: 14px 16px;
    vertical-align: middle;
}

/* EMAIL */
.text-primary-soft {
    color: #0d6efd;
    font-size: .9rem;
}

/* ESTATUS */
.estado-pill {
    padding: .4rem .9rem;
    font-size: .75rem;
    border-radius: 50px;
    font-weight: 600;
}

.estado-pill.activo {
    background: rgba(25,135,84,.15);
    color: var(--success);
}

.estado-pill.inactivo {
    background: rgba(220,53,69,.15);
    color: var(--danger);
}

/* SWITCH */
.switch {
    position: relative;
    width: 46px;
    height: 24px;
    display: inline-block;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    inset: 0;
    background: var(--danger);
    border-radius: 30px;
    transition: .3s;
    cursor: pointer;
}

.slider::before {
    content: "";
    position: absolute;
    width: 18px;
    height: 18px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .3s;
    box-shadow: 0 3px 8px rgba(0,0,0,.3);
}

input:checked + .slider {
    background: var(--success);
}

input:checked + .slider::before {
    transform: translateX(22px);
}
</style>
