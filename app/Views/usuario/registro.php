<main class="login-page">

<section class="login-section">
    <div class="login-card" style="max-width:640px;">

        <h1>Registrarme</h1>
        <p>Creá tu cuenta de cliente en New Fitness Systems</p>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if(isset($validation) && $validation->listErrors()): ?>
            <div class="alert alert-danger">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/enviar-registro') ?>" method="post">

            <div class="row g-3">

                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre *</label>
                    <input type="text" name="nombre" id="nombre" class="form-control"
                           placeholder="Ingresá tu nombre"
                           value="<?= set_value('nombre') ?>">
                </div>

                <div class="col-md-6">
                    <label for="apellido" class="form-label">Apellido *</label>
                    <input type="text" name="apellido" id="apellido" class="form-control"
                           placeholder="Ingresá tu apellido"
                           value="<?= set_value('apellido') ?>">
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Correo electrónico *</label>
                    <input type="email" name="email" id="email" class="form-control"
                           placeholder="Ingresá tu correo"
                           value="<?= set_value('email') ?>">
                </div>

                <div class="col-md-6">
                    <label for="telefono" class="form-label">Teléfono *</label>
                    <input type="text" name="telefono" id="telefono" class="form-control"
                           placeholder="Ingresá tu teléfono"
                           value="<?= set_value('telefono') ?>">
                </div>

                <div class="col-md-6">
                    <label for="dni" class="form-label">DNI *</label>
                    <input type="text" name="dni" id="dni" class="form-control"
                           placeholder="Ingresá tu DNI"
                           value="<?= set_value('dni') ?>">
                </div>

                <div class="col-md-6">
                    <label for="usuario" class="form-label">Nombre de usuario *</label>
                    <input type="text" name="usuario" id="usuario" class="form-control"
                           placeholder="Elegí tu nombre de usuario"
                           value="<?= set_value('usuario') ?>">
                </div>

                <div class="col-md-6">
                    <label for="pass" class="form-label">Contraseña *</label>
                    <input type="password" name="pass" id="pass" class="form-control"
                           placeholder="Ingresá tu contraseña">
                </div>

            </div>

            <button type="submit" class="btn-main w-100 mt-3">
                Registrarme
            </button>

        </form>

        <div class="login-links" style="text-align:center; margin-top:40px;">
            <p style="margin-bottom:5px;">¿Ya tenés cuenta?</p>

            <a href="<?= base_url('login') ?>"
               style="color:#0b8f70; font-weight:700; text-decoration:none;">
                Iniciar sesión
            </a>
        </div>

    </div>
</section>

</main>
