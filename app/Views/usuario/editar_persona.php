<main class="dashboard-page" style="align-items:flex-start;">
    
    <?= view('layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">
        <h1>Editar Persona</h1>

        <form action="<?= base_url('personas/modificar/'.$persona['id_persona']) ?>" method="post"
              style="background:#1b1b1b; padding:35px 40px; border-left:5px solid #0b8f70; max-width:1100px;">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:22px 30px;">

                <div>
                    <label style="color:white;">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= esc($persona['nombre']) ?>">
                </div>

                <div>
                    <label style="color:white;">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="<?= esc($persona['apellido']) ?>">
                </div>

                <div style="grid-column:1 / 3;">
                    <label style="color:white;">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= esc($persona['email']) ?>">
                </div>

                <div>
                    <label style="color:white;">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= esc($persona['telefono']) ?>">
                </div>

                <div>
                    <label style="color:white;">DNI</label>
                    <input type="text" name="dni" class="form-control" value="<?= esc($persona['dni']) ?>">
                </div>

                <div>
                    <label style="color:white;">Rol</label>
                    <select name="id_rol" class="form-control">
                        <?php foreach($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>" 
                                <?= $persona['id_rol'] == $rol['id_rol'] ? 'selected' : '' ?>>
                                <?= esc($rol['descripcion']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div style="margin-top:30px;">
                <button type="submit" class="btn-main">Guardar cambios</button>
                <a href="<?= base_url('personas') ?>" class="btn-main" style="background:#555;">Cancelar</a>
            </div>

        </form>
    </section>

</main>