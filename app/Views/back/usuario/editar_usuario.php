<main class="dashboard-page" style="align-items:flex-start;">
    
    <?= view('back/layout/sidebar') ?>

    <section class="dashboard-content" style="padding:50px 70px;">
        <h1>Editar Usuario</h1>

        <form action="<?= base_url('modificar_usuario/'.$usuario['id_usuario']) ?>" method="post"
              style="background:#1b1b1b; padding:35px 40px; border-left:5px solid #0b8f70; max-width:1100px;">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:22px 30px;">

                <div>
                    <label style="color:white;">Nombre</label>
                    <input type="text" name="nombre" class="form-control" value="<?= $usuario['nombre'] ?>">
                </div>

                <div>
                    <label style="color:white;">Apellido</label>
                    <input type="text" name="apellido" class="form-control" value="<?= $usuario['apellido'] ?>">
                </div>

                <div style="grid-column:1 / 3;">
                    <label style="color:white;">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $usuario['email'] ?>">
                </div>

                <div>
                    <label style="color:white;">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="<?= $usuario['telefono'] ?>">
                </div>

                <div>
                    <label style="color:white;">DNI</label>
                    <input type="text" name="dni" class="form-control" value="<?= $usuario['dni'] ?>">
                </div>

                <div>
                    <label style="color:white;">Nombre de usuario</label>
                    <input type="text" name="nombre_usuario" class="form-control" value="<?= $usuario['nombre_usuario'] ?>">
                </div>

                <div>
                    <label style="color:white;">Rol</label>
                    <select name="id_rol" class="form-control">
                        <option value="1" <?= $usuario['id_rol'] == 1 ? 'selected' : '' ?>>Administrador</option>
                        <option value="2" <?= $usuario['id_rol'] == 2 ? 'selected' : '' ?>>Profesor</option>
                        <option value="3" <?= $usuario['id_rol'] == 3 ? 'selected' : '' ?>>Cliente</option>
                    </select>
                </div>

            </div>

            <div style="margin-top:30px;">
                <button type="submit" class="btn-main">Guardar cambios</button>
                <a href="<?= base_url('usuarios') ?>" class="btn-main" style="background:#555;">Cancelar</a>
            </div>

        </form>
    </section>

</main>