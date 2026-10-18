<div class="com-tabs">
    <a href="<?= base_url('comunicaciones/vencimientos') ?>" class="<?= ($tabActiva ?? '') === 'vencimientos' ? 'active' : '' ?>">
        <i class="bi bi-calendar-x"></i> Vencimientos
    </a>
    <a href="<?= base_url('comunicaciones/abandono') ?>" class="<?= ($tabActiva ?? '') === 'abandono' ? 'active' : '' ?>">
        <i class="bi bi-person-dash"></i> Abandono
    </a>
    <a href="<?= base_url('comunicaciones/mensajes') ?>" class="<?= ($tabActiva ?? '') === 'mensajes' ? 'active' : '' ?>">
        <i class="bi bi-envelope"></i> Mensajería
    </a>
</div>

<style>
    .com-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 28px;
        border-bottom: 1px solid #262626;
    }

    .com-tabs a {
        padding: 12px 20px;
        color: #aaa;
        text-decoration: none;
        font-weight: 600;
        letter-spacing: .5px;
        border-bottom: 3px solid transparent;
        transition: .15s;
    }

    .com-tabs a:hover {
        color: #fff;
    }

    .com-tabs a.active {
        color: #0b8f70;
        border-bottom-color: #0b8f70;
    }
</style>
