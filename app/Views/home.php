<?= view('front/header') ?>
<?= view('front/navbar') ?>

<!-- HERO -->
<section class="hero d-flex align-items-center text-center">
    <div class="container">
        <h1>NEW FITNESS SYSTEMS</h1>
        <p class="lead">Transformá tu cuerpo, superá tus límites</p>
        <a href="#" class="btn btn-main mt-3">Inscribirse</a>
    </div>
</section>

<!-- ENTRENAMIENTOS -->
<section class="section-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>Entrenamientos</h2>
                <p>Elegí la disciplina ideal para vos y entrená a tu ritmo con opciones para todos los niveles.</p>
                <a href="#" class="btn btn-main">Ver más</a>
            </div>

            <div class="col-md-6">
                <img src="<?= base_url('assets/img/bp.jpg') ?>" class="img-fluid rounded shadow">            
            </div>
        </div>
    </div>
</section>

<!-- PROGRAMAS -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Nuestros Programas</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-program shadow">
                    <div class="card-body text-center">
                        <h4>BodyPump</h4>
                        <p>Entrenamiento de fuerza con barra y música.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-program shadow">
                    <div class="card-body text-center">
                        <h4>Funcional</h4>
                        <p>Mejora resistencia, fuerza y movilidad.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-program shadow">
                    <div class="card-body text-center">
                        <h4>Musculación</h4>
                        <p>Planes personalizados de hipertrofia.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= view('front/footer') ?>