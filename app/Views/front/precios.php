<main>

<section class="page-hero">
    <div class="container">
        <h1>Precios</h1>
        <p>Elegí la actividad que mejor se adapte a vos.</p>
    </div>
</section>

<section class="pricing-section" style="background-color:#111; color:white; padding:60px 0;">
    <div class="container">

        <div class="text-center mb-3">
            <span class="section-label">Planes disponibles</span>
            <h2 style="font-weight:900;">Elegí tu sistema</h2>
        </div>

        <div class="row g-4">

            <?php if (!empty($precios)): ?>
                <?php foreach ($precios as $item): ?>
                    <div class="col-md-4">
                        <div class="pricing-card text-center" style="background:#1b1b1b; border-left:4px solid #0b8f70; color:white; padding:40px 30px;">

                            <h3 style="margin-bottom:15px;">
                                <?= esc($item['nombre_sistema']) ?>
                            </h3>

                            <div class="price" style="color:#0b8f70; margin-bottom:20px;">
                                $<?= number_format($item['precio'] ?? 0, 0, ',', '.') ?>
                                <span style="font-size:14px; color:#aaa;">/mes</span>
                            </div>

                            <a href="<?= base_url('contacto') ?>" class="btn-main">
                                Inscribirme
                            </a>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p style="color:#aaa;">No hay sistemas disponibles por el momento.</p>
                </div>
            <?php endif; ?>

        </div>

    </div>
</section>

</main>