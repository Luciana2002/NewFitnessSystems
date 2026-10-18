<style>
    .reporte-acciones {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    @media print {
        .dashboard-sidebar,
        .sidebar-handle,
        .sidebar-close,
        .sidebar-overlay,
        .no-print,
        .reporte-acciones,
        nav,
        footer,
        header {
            display: none !important;
        }

        body,
        .dashboard-page,
        .dashboard-content {
            background: #ffffff !important;
        }

        .dashboard-content {
            padding: 0 !important;
            margin: 0 !important;
        }

        .dashboard-card {
            background: #ffffff !important;
            border: 1px solid #dddddd !important;
            box-shadow: none !important;
            color: #000000 !important;
        }

        .dashboard-card *,
        .dashboard-content h1,
        .dashboard-content h2,
        .dashboard-content h3,
        .dashboard-content h4,
        .dashboard-content h5,
        .dashboard-content p,
        .dashboard-content span,
        .dashboard-content strong,
        .dashboard-content td,
        .dashboard-content th,
        .dashboard-content div,
        .dashboard-content small,
        .dashboard-content label {
            color: #000000 !important;
        }

        .table,
        .table-dark,
        .table tbody,
        .table thead,
        .table tr,
        .table td,
        .table th {
            background: #ffffff !important;
            color: #000000 !important;
            border-color: #cccccc !important;
        }

        a {
            color: #000000 !important;
            text-decoration: none !important;
        }
    }
</style>
