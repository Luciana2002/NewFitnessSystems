<main class="login-page">

<section class="login-section">
    <div class="login-card">

        <h1>Iniciar sesión</h1>
        <p>Accedé al sistema de New Fitness Systems</p>

        <?php if(session()->getFlashdata('msg')): ?>
            <div class="alert alert-warning">
                <?= session()->getFlashdata('msg') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('/enviarlogin') ?>" method="post">

            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control" placeholder="Ingresá tu correo">
            </div>

            <div class="mb-2">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="pass" class="form-control" placeholder="Ingresá tu contraseña">
            </div>

            <div style="margin-bottom: 15px;">
                <a href="#" class="login-link" style="font-size: 13px;">
                    ¿Olvidaste tu contraseña?
                </a>
            </div> 

            <button type="submit" class="btn-main w-100">
                Entrar
            </button>

        </form>

        <div class="login-links" style="text-align:center; margin-top:40px;">
            <p style="margin-bottom:5px;">¿No tenés cuenta?</p>

            <a href="<?= base_url('registro') ?>" 
               style="color:#0b8f70; font-weight:700; text-decoration:none;">
                Registrarme
            </a>
        </div>

    </div>
</section>

</main>