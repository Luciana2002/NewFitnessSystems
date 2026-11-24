/* ============================================================================
   NewFitnessSystems - ESQUEMA + DATOS
   Generado: 2026-11-24 10:02:16
   Fuente: localhost\SQLEXPRESS01 / NewFitnessSystems
   Pegar y ejecutar en SSMS (sqlcmd). Usa GO como separador de lotes.

   Si las tablas ya existen, este script dara error de "ya existe".
   Para empezar de cero, descomente el bloque DROP DATABASE de abajo.
   ============================================================================ */

IF DB_ID('NewFitnessSystems') IS NULL
BEGIN
    CREATE DATABASE [NewFitnessSystems];
END;
GO

/* DESCOMENTAR SOLO SI QUIERE BORRAR LA BASE ACTUAL:
IF DB_ID('NewFitnessSystems') IS NOT NULL
    DROP DATABASE [NewFitnessSystems];
GO
CREATE DATABASE [NewFitnessSystems];
GO
*/

USE [NewFitnessSystems];
GO

SET NOCOUNT ON;
GO

PRINT 'Creando tabla [dbo].[Asistencia]';
GO
CREATE TABLE [dbo].[Asistencia] (
    [id_asistencia] int IDENTITY(1,1)  NOT NULL,
    [id_persona] int  NOT NULL,
    [fecha] date  NOT NULL,
    [hora] varchar(8)  NOT NULL,
    [resultado] varchar(20)  NOT NULL,
    [id_usuario] int  NULL,
    [observacion] varchar(200)  NULL,
    [fecha_registro] datetime  CONSTRAINT [DF__Asistenci__fecha__73BA3083] DEFAULT (getdate()) NOT NULL,
    CONSTRAINT [PK__Asistenc__D0454A9A805CBFFE] PRIMARY KEY CLUSTERED ([id_asistencia] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Bitacora]';
GO
CREATE TABLE [dbo].[Bitacora] (
    [id_log] int IDENTITY(1,1)  NOT NULL,
    [fecha] datetime  CONSTRAINT [DF_Bitacora_fecha] DEFAULT (getdate()) NOT NULL,
    [id_usuario] int  NULL,
    [nombre_usuario] nvarchar(100)  NULL,
    [accion] nvarchar(30)  NOT NULL,
    [entidad] nvarchar(50)  NOT NULL,
    [id_entidad] int  NULL,
    [detalle] nvarchar(500)  NULL,
    CONSTRAINT [PK_Bitacora] PRIMARY KEY CLUSTERED ([id_log] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Comentario]';
GO
CREATE TABLE [dbo].[Comentario] (
    [id_comentario] int IDENTITY(1,1)  NOT NULL,
    [id_persona] int  NOT NULL,
    [tipo] varchar(20)  CONSTRAINT [DF__Comentario__tipo__65370702] DEFAULT ('comentario') NOT NULL,
    [mensaje] varchar(1000)  NOT NULL,
    [fecha_registro] datetime  CONSTRAINT [DF__Comentari__fecha__662B2B3B] DEFAULT (getdate()) NOT NULL,
    [leido] bit  CONSTRAINT [DF__Comentari__leido__671F4F74] DEFAULT ((0)) NOT NULL,
    CONSTRAINT [PK__Comentar__1BA6C6F42CC801E6] PRIMARY KEY CLUSTERED ([id_comentario] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Configuracion]';
GO
CREATE TABLE [dbo].[Configuracion] (
    [clave] varchar(50)  NOT NULL,
    [valor] varchar(255)  NULL,
    CONSTRAINT [PK__Configur__71DCA3DAFDCB8237] PRIMARY KEY CLUSTERED ([clave] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Estado_suscripcion]';
GO
CREATE TABLE [dbo].[Estado_suscripcion] (
    [id_estado] int IDENTITY(1,1)  NOT NULL,
    [descripcion] varchar(50)  NOT NULL,
    CONSTRAINT [PK__Estado_s__86989FB28397EE7B] PRIMARY KEY CLUSTERED ([id_estado] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Gasto]';
GO
CREATE TABLE [dbo].[Gasto] (
    [id_gasto] int IDENTITY(1,1)  NOT NULL,
    [fecha] date  NOT NULL,
    [categoria] nvarchar(50)  NOT NULL,
    [monto] decimal(10,2)  NOT NULL,
    [detalle] nvarchar(255)  NULL,
    [id_usuario] int  NULL,
    [baja] char(1)  CONSTRAINT [DF_Gasto_baja] DEFAULT ('N') NOT NULL,
    [fecha_alta] datetime  CONSTRAINT [DF_Gasto_fecha_alta] DEFAULT (getdate()) NOT NULL,
    CONSTRAINT [PK_Gasto] PRIMARY KEY CLUSTERED ([id_gasto] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Horario]';
GO
CREATE TABLE [dbo].[Horario] (
    [id_horario] int IDENTITY(1,1)  NOT NULL,
    [hora_inicio] time(7)  NOT NULL,
    [hora_fin] time(7)  NOT NULL,
    [dia_semana] varchar(20)  NOT NULL,
    [id_sistema] int  NOT NULL,
    [baja] varchar(2)  CONSTRAINT [DF__Horario__baja__4316F928] DEFAULT ('N') NOT NULL,
    [cupo_max] int  NULL,
    CONSTRAINT [PK__Horario__C5836D699A10831D] PRIMARY KEY CLUSTERED ([id_horario] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Inscripcion]';
GO
CREATE TABLE [dbo].[Inscripcion] (
    [id_inscripcion] int IDENTITY(1,1)  NOT NULL,
    [fecha_inscripcion] date  NOT NULL,
    [id_persona] int  NOT NULL,
    [id_sistema] int  NOT NULL,
    [id_horario] int  NULL,
    CONSTRAINT [PK__Inscripc__CB0117BA90262015] PRIMARY KEY CLUSTERED ([id_inscripcion] ASC),
    CONSTRAINT [UQ_inscripcion] UNIQUE NONCLUSTERED ([id_persona] ASC, [id_sistema] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Medio_pago]';
GO
CREATE TABLE [dbo].[Medio_pago] (
    [id_medio_pago] int IDENTITY(1,1)  NOT NULL,
    [descripcion] varchar(50)  NOT NULL,
    CONSTRAINT [PK__Medio_pa__19467AD560119D4E] PRIMARY KEY CLUSTERED ([id_medio_pago] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Mensualidad]';
GO
CREATE TABLE [dbo].[Mensualidad] (
    [id_mensualidad] int IDENTITY(1,1)  NOT NULL,
    [precio] decimal(10,2)  NOT NULL,
    [fecha_vigencia] date  NOT NULL,
    [id_sistema] int  NOT NULL,
    CONSTRAINT [PK__Mensuali__D9C5A19592B56F39] PRIMARY KEY CLUSTERED ([id_mensualidad] ASC)
);
GO

PRINT 'Creando tabla [dbo].[MotivoBaja]';
GO
CREATE TABLE [dbo].[MotivoBaja] (
    [id_motivo_baja] int IDENTITY(1,1)  NOT NULL,
    [id_inscripcion] int  NULL,
    [id_persona] int  NULL,
    [motivo] varchar(500)  NOT NULL,
    [fecha_registro] datetime  CONSTRAINT [DF__MotivoBaj__fecha__625A9A57] DEFAULT (getdate()) NOT NULL,
    CONSTRAINT [PK__MotivoBa__5C31032C731C61CD] PRIMARY KEY CLUSTERED ([id_motivo_baja] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Pago]';
GO
CREATE TABLE [dbo].[Pago] (
    [id_pago] int IDENTITY(1,1)  NOT NULL,
    [fecha_pago] date  NOT NULL,
    [monto] decimal(10,2)  NOT NULL,
    [id_medio_pago] int  NOT NULL,
    [id_mensualidad] int  NULL,
    [cobrado_por] int  NOT NULL,
    [id_suscripcion] int  NOT NULL,
    CONSTRAINT [PK__Pago__0941B0740DBF8EC4] PRIMARY KEY CLUSTERED ([id_pago] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Persona]';
GO
CREATE TABLE [dbo].[Persona] (
    [id_persona] int IDENTITY(1,1)  NOT NULL,
    [nombre] varchar(50)  NOT NULL,
    [apellido] varchar(50)  NOT NULL,
    [email] varchar(100)  NOT NULL,
    [telefono] varchar(20)  NOT NULL,
    [dni] varchar(20)  NOT NULL,
    [id_rol] int  NOT NULL,
    [baja] varchar(2)  CONSTRAINT [DF__Persona__baja__3A81B327] DEFAULT ('N') NOT NULL,
    CONSTRAINT [PK__Persona__228148B0104652DD] PRIMARY KEY CLUSTERED ([id_persona] ASC),
    CONSTRAINT [UQ__Persona__D87608A75230F0FA] UNIQUE NONCLUSTERED ([dni] ASC)
);
GO

PRINT 'Creando tabla [dbo].[PlantillaMensaje]';
GO
CREATE TABLE [dbo].[PlantillaMensaje] (
    [id_plantilla] int IDENTITY(1,1)  NOT NULL,
    [titulo] nvarchar(100)  NOT NULL,
    [categoria] nvarchar(50)  NULL,
    [mensaje] nvarchar(1000)  NOT NULL,
    [baja] bit  CONSTRAINT [DF_PlantillaMensaje_baja] DEFAULT ((0)) NOT NULL,
    CONSTRAINT [PK__Plantill__2B54D0E7D7104153] PRIMARY KEY CLUSTERED ([id_plantilla] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Preferencia_usuario]';
GO
CREATE TABLE [dbo].[Preferencia_usuario] (
    [id_preferencia] int IDENTITY(1,1)  NOT NULL,
    [id_usuario] int  NOT NULL,
    [tema] varchar(10)  CONSTRAINT [DF_Pref_tema] DEFAULT ('dark') NOT NULL,
    [notif_email] bit  CONSTRAINT [DF_Pref_notif] DEFAULT ((1)) NOT NULL,
    [notif_dias] int  CONSTRAINT [DF_Pref_notifd] DEFAULT ((7)) NOT NULL,
    CONSTRAINT [PK_Preferencia_usuario] PRIMARY KEY CLUSTERED ([id_preferencia] ASC),
    CONSTRAINT [UQ_Preferencia_usuario_usuario] UNIQUE NONCLUSTERED ([id_usuario] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Reserva]';
GO
CREATE TABLE [dbo].[Reserva] (
    [id_reserva] int IDENTITY(1,1)  NOT NULL,
    [id_horario] int  NOT NULL,
    [id_persona] int  NOT NULL,
    [fecha] date  NOT NULL,
    [estado] varchar(20)  CONSTRAINT [DF__Reserva__estado__6E01572D] DEFAULT ('RESERVADA') NOT NULL,
    [id_usuario_inscripcion] int  NULL,
    [observacion] varchar(200)  NULL,
    [fecha_creacion] datetime  CONSTRAINT [DF__Reserva__fecha_c__6EF57B66] DEFAULT (getdate()) NOT NULL,
    CONSTRAINT [PK__Reserva__423CBE5D0DE4A14F] PRIMARY KEY CLUSTERED ([id_reserva] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Rol]';
GO
CREATE TABLE [dbo].[Rol] (
    [id_rol] int IDENTITY(1,1)  NOT NULL,
    [descripcion] varchar(50)  NOT NULL,
    CONSTRAINT [PK__Rol__6ABCB5E09D8BEA4A] PRIMARY KEY CLUSTERED ([id_rol] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Rutina]';
GO
CREATE TABLE [dbo].[Rutina] (
    [id_rutina] int IDENTITY(1,1)  NOT NULL,
    [id_profesor] int  NOT NULL,
    [id_cliente] int  NULL,
    [nombre] varchar(100)  NOT NULL,
    [descripcion] varchar(500)  NULL,
    [fecha] date  CONSTRAINT [DF__Rutina__fecha__5FB337D6] DEFAULT (CONVERT([date],getdate())) NOT NULL,
    [baja] varchar(2)  CONSTRAINT [DF__Rutina__baja__60A75C0F] DEFAULT ('N') NOT NULL,
    [fecha_creacion] datetime  CONSTRAINT [DF__Rutina__fecha_cr__619B8048] DEFAULT (getdate()) NOT NULL,
    [para_todos] bit  CONSTRAINT [DF__Rutina__para_tod__628FA481] DEFAULT ((0)) NOT NULL,
    CONSTRAINT [PK__Rutina__A2849667025F15BD] PRIMARY KEY CLUSTERED ([id_rutina] ASC)
);
GO

PRINT 'Creando tabla [dbo].[RutinaDia]';
GO
CREATE TABLE [dbo].[RutinaDia] (
    [id_rutina] int  NOT NULL,
    [dia_semana] int  NOT NULL,
    CONSTRAINT [PK_RutinaDia] PRIMARY KEY CLUSTERED ([id_rutina] ASC, [dia_semana] ASC)
);
GO

PRINT 'Creando tabla [dbo].[RutinaEjercicio]';
GO
CREATE TABLE [dbo].[RutinaEjercicio] (
    [id_ejercicio] int IDENTITY(1,1)  NOT NULL,
    [id_rutina] int  NOT NULL,
    [nombre] varchar(100)  NOT NULL,
    [series] varchar(20)  NULL,
    [repeticiones] varchar(20)  NULL,
    [peso] varchar(20)  NULL,
    [descanso] varchar(20)  NULL,
    [orden] int  CONSTRAINT [DF__RutinaEje__orden__6754599E] DEFAULT ((0)) NOT NULL,
    [observacion] varchar(200)  NULL,
    CONSTRAINT [PK__RutinaEj__7BB4D9DBC7B4F5B0] PRIMARY KEY CLUSTERED ([id_ejercicio] ASC)
);
GO

PRINT 'Creando tabla [dbo].[SeguimientoFisico]';
GO
CREATE TABLE [dbo].[SeguimientoFisico] (
    [id_seguimiento] int IDENTITY(1,1)  NOT NULL,
    [id_persona] int  NOT NULL,
    [fecha] date  NOT NULL,
    [peso] decimal(6,2)  NULL,
    [grasa_corporal] decimal(5,2)  NULL,
    [cintura] decimal(6,2)  NULL,
    [pecho] decimal(6,2)  NULL,
    [brazo] decimal(6,2)  NULL,
    [muslo] decimal(6,2)  NULL,
    [cadera] decimal(6,2)  NULL,
    [notas] varchar(300)  NULL,
    [fecha_registro] datetime  CONSTRAINT [DF__Seguimien__fecha__778AC167] DEFAULT (getdate()) NOT NULL,
    CONSTRAINT [PK__Seguimie__127E9A1AF87A45A8] PRIMARY KEY CLUSTERED ([id_seguimiento] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Sistema]';
GO
CREATE TABLE [dbo].[Sistema] (
    [id_sistema] int IDENTITY(1,1)  NOT NULL,
    [nombre_sistema] varchar(50)  NOT NULL,
    [color] varchar(7)  CONSTRAINT [DF__Sistema__color__3E52440B] DEFAULT ('#0b8f70') NOT NULL,
    [baja] varchar(2)  CONSTRAINT [DF__Sistema__baja__3F466844] DEFAULT ('N') NOT NULL,
    [acceso_libre] bit  CONSTRAINT [DF_Sistema_acceso_libre] DEFAULT ((0)) NOT NULL,
    CONSTRAINT [PK__Sistema__A0747B26FF344883] PRIMARY KEY CLUSTERED ([id_sistema] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Suscripcion]';
GO
CREATE TABLE [dbo].[Suscripcion] (
    [id_suscripcion] int IDENTITY(1,1)  NOT NULL,
    [fecha_inicio] date  NOT NULL,
    [fecha_vencimiento] date  NOT NULL,
    [id_estado] int  NOT NULL,
    [id_inscripcion] int  NOT NULL,
    CONSTRAINT [PK__Suscripc__4E8926BBCD8BDF73] PRIMARY KEY CLUSTERED ([id_suscripcion] ASC)
);
GO

PRINT 'Creando tabla [dbo].[Usuario]';
GO
CREATE TABLE [dbo].[Usuario] (
    [id_usuario] int IDENTITY(1,1)  NOT NULL,
    [nombre_usuario] varchar(50)  NOT NULL,
    [contraseña] varchar(255)  NOT NULL,
    [id_persona] int  NOT NULL,
    CONSTRAINT [PK__Usuario__4E3E04ADC5984BA0] PRIMARY KEY CLUSTERED ([id_usuario] ASC),
    CONSTRAINT [UQ__Usuario__D4D22D74A2D1A12C] UNIQUE NONCLUSTERED ([nombre_usuario] ASC)
);
GO

PRINT 'Insertando datos';
GO

PRINT 'Datos: [dbo].[Asistencia] (150 filas)';
SET IDENTITY_INSERT [dbo].[Asistencia] ON;
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (1, 6, N'2026-10-17', N'07:00:00', N'VENCIDA', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (2, 7, N'2026-10-16', N'08:07:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (3, 8, N'2026-10-15', N'09:14:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (4, 9, N'2026-10-14', N'10:21:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (5, 10, N'2026-10-13', N'11:28:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (6, 11, N'2026-10-12', N'12:35:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (7, 12, N'2026-10-11', N'13:42:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (8, 13, N'2026-10-10', N'14:49:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (9, 14, N'2026-10-09', N'15:56:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (10, 15, N'2026-10-08', N'16:03:00', N'VENCIDA', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (11, 16, N'2026-10-07', N'17:10:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (12, 17, N'2026-10-06', N'18:17:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (13, 18, N'2026-10-05', N'19:24:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (14, 19, N'2026-10-04', N'20:31:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (15, 20, N'2026-10-03', N'21:38:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (16, 21, N'2026-10-02', N'07:45:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (17, 22, N'2026-10-01', N'08:52:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (18, 23, N'2026-09-30', N'09:59:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (19, 24, N'2026-09-29', N'10:06:00', N'VENCIDA', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (20, 25, N'2026-09-28', N'11:13:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (21, 26, N'2026-09-27', N'12:20:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (22, 27, N'2026-10-17', N'13:27:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (23, 28, N'2026-10-16', N'14:34:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (24, 29, N'2026-10-15', N'15:41:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (25, 30, N'2026-10-14', N'16:48:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (26, 31, N'2026-10-13', N'17:55:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (27, 32, N'2026-10-12', N'18:02:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (28, 33, N'2026-10-11', N'19:09:00', N'VENCIDA', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (29, 34, N'2026-10-10', N'20:16:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (30, 35, N'2026-10-09', N'21:23:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (31, 36, N'2026-10-08', N'07:30:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (32, 37, N'2026-10-07', N'08:37:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (33, 38, N'2026-10-06', N'09:44:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (34, 39, N'2026-10-05', N'10:51:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (35, 40, N'2026-10-04', N'11:58:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (36, 41, N'2026-10-03', N'12:05:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (37, 42, N'2026-10-02', N'13:12:00', N'VENCIDA', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (38, 43, N'2026-10-01', N'14:19:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (39, 44, N'2026-09-30', N'15:26:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (40, 45, N'2026-09-29', N'16:33:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (41, 46, N'2026-09-28', N'17:40:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (42, 47, N'2026-09-27', N'18:47:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (43, 48, N'2026-10-17', N'19:54:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (44, 49, N'2026-10-16', N'20:01:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (45, 50, N'2026-10-15', N'21:08:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (46, 51, N'2026-10-14', N'07:15:00', N'VENCIDA', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (47, 52, N'2026-10-13', N'08:22:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (48, 53, N'2026-10-12', N'09:29:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (49, 54, N'2026-10-11', N'10:36:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (50, 55, N'2026-10-10', N'11:43:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (51, 6, N'2026-10-09', N'12:50:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (52, 7, N'2026-10-08', N'13:57:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (53, 8, N'2026-10-07', N'14:04:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (54, 9, N'2026-10-06', N'15:11:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (55, 10, N'2026-10-05', N'16:18:00', N'VENCIDA', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (56, 11, N'2026-10-04', N'17:25:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (57, 12, N'2026-10-03', N'18:32:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (58, 13, N'2026-10-02', N'19:39:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (59, 14, N'2026-10-01', N'20:46:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (60, 15, N'2026-09-30', N'21:53:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (61, 16, N'2026-09-29', N'07:00:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (62, 17, N'2026-09-28', N'08:07:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (63, 18, N'2026-09-27', N'09:14:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (64, 19, N'2026-10-17', N'10:21:00', N'VENCIDA', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (65, 20, N'2026-10-16', N'11:28:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (66, 21, N'2026-10-15', N'12:35:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (67, 22, N'2026-10-14', N'13:42:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (68, 23, N'2026-10-13', N'14:49:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (69, 24, N'2026-10-12', N'15:56:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (70, 25, N'2026-10-11', N'16:03:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (71, 26, N'2026-10-10', N'17:10:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (72, 27, N'2026-10-09', N'18:17:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (73, 28, N'2026-10-08', N'19:24:00', N'VENCIDA', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (74, 29, N'2026-10-07', N'20:31:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (75, 30, N'2026-10-06', N'21:38:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (76, 31, N'2026-10-05', N'07:45:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (77, 32, N'2026-10-04', N'08:52:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (78, 33, N'2026-10-03', N'09:59:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (79, 34, N'2026-10-02', N'10:06:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (80, 35, N'2026-10-01', N'11:13:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (81, 36, N'2026-09-30', N'12:20:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (82, 37, N'2026-09-29', N'13:27:00', N'VENCIDA', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (83, 38, N'2026-09-28', N'14:34:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (84, 39, N'2026-09-27', N'15:41:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (85, 40, N'2026-10-17', N'16:48:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (86, 41, N'2026-10-16', N'17:55:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (87, 42, N'2026-10-15', N'18:02:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (88, 43, N'2026-10-14', N'19:09:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (89, 44, N'2026-10-13', N'20:16:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (90, 45, N'2026-10-12', N'21:23:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (91, 46, N'2026-10-11', N'07:30:00', N'VENCIDA', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (92, 47, N'2026-10-10', N'08:37:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (93, 48, N'2026-10-09', N'09:44:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (94, 49, N'2026-10-08', N'10:51:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (95, 50, N'2026-10-07', N'11:58:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (96, 51, N'2026-10-06', N'12:05:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (97, 52, N'2026-10-05', N'13:12:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (98, 53, N'2026-10-04', N'14:19:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (99, 54, N'2026-10-03', N'15:26:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (100, 55, N'2026-10-02', N'16:33:00', N'VENCIDA', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (101, 6, N'2026-10-01', N'17:40:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (102, 7, N'2026-09-30', N'18:47:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (103, 8, N'2026-09-29', N'19:54:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (104, 9, N'2026-09-28', N'20:01:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (105, 10, N'2026-09-27', N'21:08:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (106, 11, N'2026-10-17', N'07:15:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (107, 12, N'2026-10-16', N'08:22:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (108, 13, N'2026-10-15', N'09:29:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (109, 14, N'2026-10-14', N'10:36:00', N'VENCIDA', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (110, 15, N'2026-10-13', N'11:43:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (111, 16, N'2026-10-12', N'12:50:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (112, 17, N'2026-10-11', N'13:57:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (113, 18, N'2026-10-10', N'14:04:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (114, 19, N'2026-10-09', N'15:11:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (115, 20, N'2026-10-08', N'16:18:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (116, 21, N'2026-10-07', N'17:25:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (117, 22, N'2026-10-06', N'18:32:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (118, 23, N'2026-10-05', N'19:39:00', N'VENCIDA', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (119, 24, N'2026-10-04', N'20:46:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (120, 25, N'2026-10-03', N'21:53:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (121, 26, N'2026-10-02', N'07:00:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (122, 27, N'2026-10-01', N'08:07:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (123, 28, N'2026-09-30', N'09:14:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (124, 29, N'2026-09-29', N'10:21:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (125, 30, N'2026-09-28', N'11:28:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (126, 31, N'2026-09-27', N'12:35:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (127, 32, N'2026-10-17', N'13:42:00', N'VENCIDA', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (128, 33, N'2026-10-16', N'14:49:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (129, 34, N'2026-10-15', N'15:56:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (130, 35, N'2026-10-14', N'16:03:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (131, 36, N'2026-10-13', N'17:10:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (132, 37, N'2026-10-12', N'18:17:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (133, 38, N'2026-10-11', N'19:24:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (134, 39, N'2026-10-10', N'20:31:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (135, 40, N'2026-10-09', N'21:38:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (136, 41, N'2026-10-08', N'07:45:00', N'VENCIDA', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (137, 42, N'2026-10-07', N'08:52:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (138, 43, N'2026-10-06', N'09:59:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (139, 44, N'2026-10-05', N'10:06:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (140, 45, N'2026-10-04', N'11:13:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (141, 46, N'2026-10-03', N'12:20:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (142, 47, N'2026-10-02', N'13:27:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (143, 48, N'2026-10-01', N'14:34:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (144, 49, N'2026-09-30', N'15:41:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (145, 50, N'2026-09-29', N'16:48:00', N'VENCIDA', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (146, 51, N'2026-09-28', N'17:55:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (147, 52, N'2026-09-27', N'18:02:00', N'OK', 4, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (148, 53, N'2026-10-17', N'19:09:00', N'OK', 5, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (149, 54, N'2026-10-16', N'20:16:00', N'OK', 2, NULL, N'2026-10-17T00:49:41.327');
INSERT INTO [dbo].[Asistencia] ([id_asistencia], [id_persona], [fecha], [hora], [resultado], [id_usuario], [observacion], [fecha_registro]) VALUES (150, 55, N'2026-10-15', N'21:23:00', N'OK', 3, NULL, N'2026-10-17T00:49:41.327');
SET IDENTITY_INSERT [dbo].[Asistencia] OFF;
GO

PRINT 'Datos: [dbo].[Bitacora] (254 filas)';
SET IDENTITY_INSERT [dbo].[Bitacora] ON;
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (9, N'2026-07-01T09:15:00.000', 1, N'admin', N'ALTA', N'Gasto', 10, N'Gasto registrado: Alquiler del local - julio 2026 ($80.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (10, N'2026-07-10T09:40:00.000', 1, N'admin', N'ALTA', N'Gasto', 13, N'Gasto registrado: Servicios (luz, agua e internet) - julio 2026 ($12.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (11, N'2026-08-01T09:12:00.000', 1, N'admin', N'ALTA', N'Gasto', 11, N'Gasto registrado: Alquiler del local - agosto 2026 ($80.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (12, N'2026-08-10T09:35:00.000', 1, N'admin', N'ALTA', N'Gasto', 14, N'Gasto registrado: Servicios (luz, agua e internet) - agosto 2026 ($12.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (13, N'2026-09-01T09:10:00.000', 1, N'admin', N'ALTA', N'Gasto', 12, N'Gasto registrado: Alquiler del local - septiembre 2026 ($80.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (14, N'2026-09-10T09:30:00.000', 1, N'admin', N'ALTA', N'Gasto', 15, N'Gasto registrado: Servicios (luz, agua e internet) - septiembre 2026 ($12.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (15, N'2026-10-17T10:30:00.000', 1, N'admin', N'EDICION', N'Configuracion', NULL, N'Comision de profesores actualizada a 30%');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (16, N'2026-07-02T10:12:00.000', 1, N'admin', N'ALTA', N'Cliente', 6, N'Alta de cliente: Juan Perez');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (17, N'2026-07-02T10:12:30.000', 1, N'admin', N'ALTA', N'Usuario', 6, N'Creacion de usuario: juanp (cliente)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (18, N'2026-07-05T09:40:00.000', 1, N'admin', N'ALTA', N'Cliente', 7, N'Alta de cliente: Maria Lopez');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (19, N'2026-07-08T11:05:00.000', 1, N'admin', N'PRECIO', N'Sistema', 2, N'Precio de PowerJump actualizado de $22.000 a $25.000');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (20, N'2026-07-10T09:10:00.000', 1, N'admin', N'ALTA', N'Profesor', 2, N'Alta de profesor: Carlos Gomez (@prof_carlos)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (21, N'2026-07-15T16:22:00.000', 1, N'admin', N'ALTA', N'Cliente', 8, N'Alta de cliente: Ana Fernandez');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (22, N'2026-07-20T14:35:00.000', 1, N'admin', N'EDICION', N'Sistema', 4, N'Edicion de sistema: Zumba (color actualizado a #e91e63)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (23, N'2026-07-28T17:48:00.000', 1, N'admin', N'BAJA', N'Cliente', 11, N'Baja de cliente: Nicolas Benitez');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (24, N'2026-08-03T09:05:00.000', 1, N'admin', N'ALTA', N'Profesor', 3, N'Alta de profesor: Mariana Ruiz (@prof_mariana)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (25, N'2026-08-05T10:30:00.000', 1, N'admin', N'PRECIO', N'Sistema', 3, N'Precio de Funcional actualizado de $28.000 a $30.000');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (26, N'2026-08-07T08:55:00.000', 1, N'admin', N'ALTA', N'Cliente', 9, N'Alta de cliente: Diego Ramirez');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (27, N'2026-08-07T08:55:30.000', 1, N'admin', N'ALTA', N'Usuario', 9, N'Creacion de usuario: diegor (cliente)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (28, N'2026-08-12T15:10:00.000', 1, N'admin', N'EDICION', N'Cliente', 6, N'Edicion de cliente: Juan Perez (email actualizado)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (29, N'2026-08-18T12:00:00.000', 1, N'admin', N'ALTA', N'Sistema', 5, N'Alta de sistema: Artes Marciales ($32.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (30, N'2026-08-25T18:20:00.000', 1, N'admin', N'BAJA', N'Cliente', 12, N'Baja de cliente: Valentina Molina');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (31, N'2026-09-01T09:00:00.000', 1, N'admin', N'ALTA', N'Profesor', 4, N'Alta de profesor: Federico Sosa (@prof_federico)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (32, N'2026-09-02T09:15:00.000', 1, N'admin', N'ALTA', N'Profesor', 5, N'Alta de profesor: Camila Torres (@prof_camila)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (33, N'2026-09-02T09:40:00.000', 1, N'admin', N'ALTA', N'Cliente', 11, N'Reactivacion de cliente: Nicolas Benitez');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (34, N'2026-09-04T10:45:00.000', 1, N'admin', N'PRECIO', N'Sistema', 1, N'Precio de BodyPump actualizado de $26.000 a $28.000');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (35, N'2026-09-05T11:20:00.000', 1, N'admin', N'ALTA', N'Cliente', 12, N'Reactivacion de cliente: Valentina Molina');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (36, N'2026-09-08T11:30:00.000', 1, N'admin', N'ALTA', N'Cliente', 13, N'Alta de cliente: Tomas Silva');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (37, N'2026-09-10T13:05:00.000', 1, N'admin', N'EDICION', N'Usuario', 7, N'Edicion de usuario: marial (datos actualizados)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (39, N'2026-09-20T09:50:00.000', 1, N'admin', N'ALTA', N'Plantilla', NULL, N'Plantilla creada: Recordatorio de vencimiento');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (40, N'2026-09-26T17:40:00.000', 1, N'admin', N'BAJA', N'Cliente', 14, N'Baja de cliente: Martina Gimenez');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (41, N'2026-10-01T09:05:00.000', 1, N'admin', N'ALTA', N'Gasto', 1, N'Gasto registrado: Alquiler del local - octubre 2026 ($80.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (42, N'2026-10-01T09:25:00.000', 1, N'admin', N'ALTA', N'Cliente', 14, N'Reactivacion de cliente: Martina Gimenez');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (43, N'2026-10-02T10:15:00.000', 1, N'admin', N'ALTA', N'Cliente', 17, N'Alta de cliente: Mateo Herrera');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (44, N'2026-10-05T09:30:00.000', 1, N'admin', N'ALTA', N'Gasto', 2, N'Gasto registrado: Sueldos profesores - octubre 2026 ($150.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (45, N'2026-10-05T14:20:00.000', 1, N'admin', N'EDICION', N'Cliente', 7, N'Edicion de cliente: Maria Lopez (direccion actualizada)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (46, N'2026-10-07T11:10:00.000', 1, N'admin', N'PRECIO', N'Sistema', 6, N'Precio de Gimnasio actualizado de $33.000 a $35.000');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (47, N'2026-10-10T09:40:00.000', 1, N'admin', N'ALTA', N'Gasto', 3, N'Gasto registrado: Servicios (luz, agua e internet) - octubre 2026 ($12.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (49, N'2026-10-12T09:30:00.000', 1, N'admin', N'ALTA', N'Gasto', 4, N'Gasto registrado: Reparacion de equipos - octubre 2026 ($8.000)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (50, N'2026-10-12T15:00:00.000', 1, N'admin', N'EDICION', N'Sistema', 1, N'Edicion de sistema: BodyPump (color actualizado a #0b8f70)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (51, N'2026-10-13T08:50:00.000', 1, N'admin', N'ALTA', N'Usuario', 10, N'Creacion de usuario: sofiac (cliente)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (52, N'2026-10-16T16:40:00.000', 1, N'admin', N'EDICION', N'Cliente', 10, N'Edicion de cliente: Sofia Acosta (telefono actualizado)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (53, N'2026-07-01T10:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 1, N'Pago registrado: $28.000 - Juan Perez (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (54, N'2026-07-02T11:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 2, N'Pago registrado: $25.000 - Maria Lopez (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (55, N'2026-07-03T12:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 3, N'Pago registrado: $30.000 - Ana Fernandez (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (56, N'2026-07-04T13:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 4, N'Pago registrado: $26.000 - Diego Ramirez (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (57, N'2026-07-05T14:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 5, N'Pago registrado: $32.000 - Sofia Acosta (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (58, N'2026-07-06T15:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 6, N'Pago registrado: $35.000 - Nicolas Benitez (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (59, N'2026-07-07T16:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 7, N'Pago registrado: $28.000 - Valentina Molina (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (60, N'2026-07-08T17:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 8, N'Pago registrado: $30.000 - Tomas Silva (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (61, N'2026-07-09T09:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 9, N'Pago registrado: $25.000 - Martina Gimenez (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (62, N'2026-07-10T10:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 10, N'Pago registrado: $26.000 - Lucas Castro (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (63, N'2026-07-11T11:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 11, N'Pago registrado: $32.000 - Florencia Rojas (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (64, N'2026-07-12T12:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 12, N'Pago registrado: $35.000 - Mateo Herrera (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (65, N'2026-08-12T13:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 13, N'Pago registrado: $35.000 - Mateo Herrera (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (66, N'2026-09-12T14:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 14, N'Pago registrado: $35.000 - Mateo Herrera (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (67, N'2026-10-12T15:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 15, N'Pago registrado: $35.000 - Mateo Herrera (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (68, N'2026-07-13T16:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 16, N'Pago registrado: $28.000 - Julieta Vera (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (69, N'2026-08-13T17:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 17, N'Pago registrado: $28.000 - Julieta Vera (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (70, N'2026-09-13T09:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 18, N'Pago registrado: $28.000 - Julieta Vera (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (71, N'2026-10-13T10:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 19, N'Pago registrado: $28.000 - Julieta Vera (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (72, N'2026-07-14T11:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 20, N'Pago registrado: $25.000 - Santiago Aguirre (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (73, N'2026-08-14T12:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 21, N'Pago registrado: $25.000 - Santiago Aguirre (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (74, N'2026-09-14T13:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 22, N'Pago registrado: $25.000 - Santiago Aguirre (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (75, N'2026-10-14T14:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 23, N'Pago registrado: $25.000 - Santiago Aguirre (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (76, N'2026-07-15T15:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 24, N'Pago registrado: $30.000 - Carla Medina (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (77, N'2026-08-15T16:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 25, N'Pago registrado: $30.000 - Carla Medina (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (78, N'2026-09-15T17:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 26, N'Pago registrado: $30.000 - Carla Medina (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (79, N'2026-10-15T09:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 27, N'Pago registrado: $30.000 - Carla Medina (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (80, N'2026-07-16T10:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 28, N'Pago registrado: $26.000 - Bruno Luna (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (81, N'2026-08-16T11:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 29, N'Pago registrado: $26.000 - Bruno Luna (Zumba - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (82, N'2026-09-16T12:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 30, N'Pago registrado: $26.000 - Bruno Luna (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (83, N'2026-10-16T13:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 31, N'Pago registrado: $26.000 - Bruno Luna (Zumba - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (84, N'2026-07-17T14:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 32, N'Pago registrado: $32.000 - Micaela Ortiz (Artes Marciales - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (85, N'2026-08-17T15:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 33, N'Pago registrado: $32.000 - Micaela Ortiz (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (86, N'2026-09-17T16:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 34, N'Pago registrado: $32.000 - Micaela Ortiz (Artes Marciales - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (87, N'2026-10-17T17:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 35, N'Pago registrado: $32.000 - Micaela Ortiz (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (88, N'2026-07-18T09:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 36, N'Pago registrado: $35.000 - Ezequiel Navarro (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (89, N'2026-08-18T10:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 37, N'Pago registrado: $35.000 - Ezequiel Navarro (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (90, N'2026-09-18T11:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 38, N'Pago registrado: $35.000 - Ezequiel Navarro (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (91, N'2026-07-19T12:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 39, N'Pago registrado: $28.000 - Lucia Paredes (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (92, N'2026-08-19T13:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 40, N'Pago registrado: $28.000 - Lucia Paredes (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (93, N'2026-09-19T14:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 41, N'Pago registrado: $28.000 - Lucia Paredes (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (94, N'2026-07-20T15:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 42, N'Pago registrado: $25.000 - Franco Caceres (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (95, N'2026-08-20T16:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 43, N'Pago registrado: $25.000 - Franco Caceres (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (96, N'2026-09-20T17:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 44, N'Pago registrado: $25.000 - Franco Caceres (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (97, N'2026-07-21T09:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 45, N'Pago registrado: $30.000 - Rocio Morales (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (98, N'2026-08-21T10:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 46, N'Pago registrado: $30.000 - Rocio Morales (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (99, N'2026-09-21T11:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 47, N'Pago registrado: $30.000 - Rocio Morales (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (100, N'2026-07-22T12:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 48, N'Pago registrado: $26.000 - Emiliano Paz (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (101, N'2026-08-22T13:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 49, N'Pago registrado: $26.000 - Emiliano Paz (Zumba - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (102, N'2026-09-22T14:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 50, N'Pago registrado: $26.000 - Emiliano Paz (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (103, N'2026-07-23T15:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 51, N'Pago registrado: $32.000 - Abril Romero (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (104, N'2026-08-23T16:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 52, N'Pago registrado: $32.000 - Abril Romero (Artes Marciales - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (105, N'2026-09-23T17:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 53, N'Pago registrado: $32.000 - Abril Romero (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (106, N'2026-07-24T09:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 54, N'Pago registrado: $35.000 - Gonzalo Ibarra (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (107, N'2026-08-24T10:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 55, N'Pago registrado: $35.000 - Gonzalo Ibarra (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (108, N'2026-09-24T11:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 56, N'Pago registrado: $35.000 - Gonzalo Ibarra (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (109, N'2026-07-25T12:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 57, N'Pago registrado: $28.000 - Bianca Vargas (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (110, N'2026-08-25T13:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 58, N'Pago registrado: $28.000 - Bianca Vargas (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (111, N'2026-09-25T14:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 59, N'Pago registrado: $28.000 - Bianca Vargas (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (112, N'2026-07-26T15:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 60, N'Pago registrado: $25.000 - Matias Peralta (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (113, N'2026-08-26T16:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 61, N'Pago registrado: $25.000 - Matias Peralta (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (114, N'2026-09-26T17:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 62, N'Pago registrado: $25.000 - Matias Peralta (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (115, N'2026-07-27T09:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 63, N'Pago registrado: $30.000 - Aldana Correa (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (116, N'2026-08-27T10:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 64, N'Pago registrado: $30.000 - Aldana Correa (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (117, N'2026-09-27T11:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 65, N'Pago registrado: $30.000 - Aldana Correa (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (118, N'2026-07-28T12:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 66, N'Pago registrado: $26.000 - Leandro Mansilla (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (119, N'2026-08-28T13:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 67, N'Pago registrado: $26.000 - Leandro Mansilla (Zumba - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (120, N'2026-09-28T14:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 68, N'Pago registrado: $26.000 - Leandro Mansilla (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (121, N'2026-07-29T15:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 69, N'Pago registrado: $32.000 - Milagros Cano (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (122, N'2026-08-29T16:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 70, N'Pago registrado: $32.000 - Milagros Cano (Artes Marciales - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (123, N'2026-09-29T17:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 71, N'Pago registrado: $32.000 - Milagros Cano (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (124, N'2026-07-30T09:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 72, N'Pago registrado: $35.000 - Joaquin Farias (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (125, N'2026-08-30T10:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 73, N'Pago registrado: $35.000 - Joaquin Farias (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (126, N'2026-09-30T11:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 74, N'Pago registrado: $35.000 - Joaquin Farias (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (127, N'2026-07-31T12:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 75, N'Pago registrado: $28.000 - Pilar Dominguez (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (128, N'2026-08-31T13:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 76, N'Pago registrado: $28.000 - Pilar Dominguez (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (129, N'2026-10-01T14:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 77, N'Pago registrado: $28.000 - Pilar Dominguez (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (130, N'2026-08-01T15:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 78, N'Pago registrado: $25.000 - Lautaro Sanchez (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (131, N'2026-09-01T16:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 79, N'Pago registrado: $25.000 - Lautaro Sanchez (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (132, N'2026-10-01T17:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 80, N'Pago registrado: $25.000 - Lautaro Sanchez (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (133, N'2026-08-02T09:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 81, N'Pago registrado: $30.000 - Delfina Nuñez (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (134, N'2026-09-02T10:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 82, N'Pago registrado: $30.000 - Delfina Nuñez (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (135, N'2026-10-02T11:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 83, N'Pago registrado: $30.000 - Delfina Nuñez (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (136, N'2026-08-03T12:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 84, N'Pago registrado: $26.000 - Ramiro Rios (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (137, N'2026-09-03T13:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 85, N'Pago registrado: $26.000 - Ramiro Rios (Zumba - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (138, N'2026-10-03T14:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 86, N'Pago registrado: $26.000 - Ramiro Rios (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (139, N'2026-08-04T15:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 87, N'Pago registrado: $32.000 - Candela Soto (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (140, N'2026-09-04T16:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 88, N'Pago registrado: $32.000 - Candela Soto (Artes Marciales - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (141, N'2026-10-04T17:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 89, N'Pago registrado: $32.000 - Candela Soto (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (142, N'2026-08-05T09:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 90, N'Pago registrado: $35.000 - Ivan Arias (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (143, N'2026-09-05T10:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 91, N'Pago registrado: $35.000 - Ivan Arias (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (144, N'2026-10-05T11:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 92, N'Pago registrado: $35.000 - Ivan Arias (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (145, N'2026-08-06T12:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 93, N'Pago registrado: $28.000 - Natalia Ferreyra (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (146, N'2026-09-06T13:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 94, N'Pago registrado: $28.000 - Natalia Ferreyra (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (147, N'2026-10-06T14:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 95, N'Pago registrado: $28.000 - Natalia Ferreyra (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (148, N'2026-08-07T15:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 96, N'Pago registrado: $25.000 - Maximiliano Godoy (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (149, N'2026-09-07T16:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 97, N'Pago registrado: $25.000 - Maximiliano Godoy (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (150, N'2026-10-07T17:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 98, N'Pago registrado: $25.000 - Maximiliano Godoy (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (151, N'2026-08-08T09:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 99, N'Pago registrado: $30.000 - Antonella Suarez (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (152, N'2026-09-08T10:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 100, N'Pago registrado: $30.000 - Antonella Suarez (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (153, N'2026-10-08T11:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 101, N'Pago registrado: $30.000 - Antonella Suarez (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (154, N'2026-08-09T12:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 102, N'Pago registrado: $26.000 - Sebastian Cruz (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (155, N'2026-09-09T13:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 103, N'Pago registrado: $26.000 - Sebastian Cruz (Zumba - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (156, N'2026-10-09T14:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 104, N'Pago registrado: $26.000 - Sebastian Cruz (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (157, N'2026-08-10T15:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 105, N'Pago registrado: $32.000 - Jazmin Duarte (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (158, N'2026-09-10T16:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 106, N'Pago registrado: $32.000 - Jazmin Duarte (Artes Marciales - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (159, N'2026-10-10T17:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 107, N'Pago registrado: $32.000 - Jazmin Duarte (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (160, N'2026-08-11T09:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 108, N'Pago registrado: $35.000 - Thiago Aguilera (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (161, N'2026-09-11T10:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 109, N'Pago registrado: $35.000 - Thiago Aguilera (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (162, N'2026-10-11T11:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 110, N'Pago registrado: $35.000 - Thiago Aguilera (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (163, N'2026-08-12T12:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 111, N'Pago registrado: $28.000 - Renata Cabrera (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (164, N'2026-09-12T13:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 112, N'Pago registrado: $28.000 - Renata Cabrera (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (165, N'2026-10-12T14:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 113, N'Pago registrado: $28.000 - Renata Cabrera (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (166, N'2026-08-13T15:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 114, N'Pago registrado: $25.000 - Agustin Roldan (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (167, N'2026-09-13T16:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 115, N'Pago registrado: $25.000 - Agustin Roldan (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (168, N'2026-10-13T17:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 116, N'Pago registrado: $25.000 - Agustin Roldan (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (169, N'2026-08-14T09:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 117, N'Pago registrado: $30.000 - Noelia Blanco (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (170, N'2026-09-14T10:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 118, N'Pago registrado: $30.000 - Noelia Blanco (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (171, N'2026-10-14T11:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 119, N'Pago registrado: $30.000 - Noelia Blanco (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (172, N'2026-08-15T12:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 120, N'Pago registrado: $26.000 - Bautista Escobar (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (173, N'2026-09-15T13:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 121, N'Pago registrado: $26.000 - Bautista Escobar (Zumba - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (174, N'2026-10-15T14:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 122, N'Pago registrado: $26.000 - Bautista Escobar (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (175, N'2026-08-16T15:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 123, N'Pago registrado: $32.000 - Mora Guzman (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (176, N'2026-09-16T16:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 124, N'Pago registrado: $32.000 - Mora Guzman (Artes Marciales - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (177, N'2026-10-16T17:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 125, N'Pago registrado: $32.000 - Mora Guzman (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (178, N'2026-08-17T09:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 126, N'Pago registrado: $35.000 - Santino Vega (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (179, N'2026-09-17T10:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 127, N'Pago registrado: $35.000 - Santino Vega (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (180, N'2026-10-17T11:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 128, N'Pago registrado: $35.000 - Santino Vega (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (181, N'2026-08-18T12:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 129, N'Pago registrado: $28.000 - Victoria Maidana (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (182, N'2026-09-18T13:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 130, N'Pago registrado: $28.000 - Victoria Maidana (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (183, N'2026-08-19T14:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 131, N'Pago registrado: $25.000 - Gael Quiroga (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (184, N'2026-09-19T15:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 132, N'Pago registrado: $25.000 - Gael Quiroga (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (185, N'2026-08-20T16:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 133, N'Pago registrado: $35.000 - Juan Perez (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (186, N'2026-09-20T17:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 134, N'Pago registrado: $35.000 - Juan Perez (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (187, N'2026-08-21T09:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 135, N'Pago registrado: $35.000 - Maria Lopez (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (188, N'2026-09-21T10:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 136, N'Pago registrado: $35.000 - Maria Lopez (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (189, N'2026-08-22T11:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 137, N'Pago registrado: $28.000 - Ana Fernandez (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (190, N'2026-09-22T12:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 138, N'Pago registrado: $28.000 - Ana Fernandez (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (191, N'2026-08-23T13:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 139, N'Pago registrado: $25.000 - Diego Ramirez (PowerJump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (192, N'2026-09-23T14:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 140, N'Pago registrado: $25.000 - Diego Ramirez (PowerJump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (193, N'2026-08-24T15:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 141, N'Pago registrado: $30.000 - Sofia Acosta (Funcional - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (194, N'2026-09-24T16:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 142, N'Pago registrado: $30.000 - Sofia Acosta (Funcional - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (195, N'2026-08-25T17:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 143, N'Pago registrado: $26.000 - Nicolas Benitez (Zumba - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (196, N'2026-09-25T09:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 144, N'Pago registrado: $26.000 - Nicolas Benitez (Zumba - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (197, N'2026-08-26T10:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 145, N'Pago registrado: $32.000 - Valentina Molina (Artes Marciales - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (198, N'2026-09-26T11:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 146, N'Pago registrado: $32.000 - Valentina Molina (Artes Marciales - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (199, N'2026-08-27T12:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 147, N'Pago registrado: $35.000 - Tomas Silva (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (200, N'2026-09-27T13:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 148, N'Pago registrado: $35.000 - Tomas Silva (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (201, N'2026-08-28T14:00:00.000', 2, N'prof_carlos', N'ALTA', N'Pago', 149, N'Pago registrado: $28.000 - Martina Gimenez (BodyPump - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (202, N'2026-09-28T15:00:00.000', 3, N'prof_mariana', N'ALTA', N'Pago', 150, N'Pago registrado: $28.000 - Martina Gimenez (BodyPump - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (203, N'2026-08-29T16:00:00.000', 4, N'prof_federico', N'ALTA', N'Pago', 151, N'Pago registrado: $35.000 - Lucas Castro (Gimnasio - Efectivo)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (204, N'2026-09-29T17:00:00.000', 5, N'prof_camila', N'ALTA', N'Pago', 152, N'Pago registrado: $35.000 - Lucas Castro (Gimnasio - Transferencia)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (206, N'2026-10-16T09:00:00.000', 1, N'admin', N'ALTA', N'Pago', 153, N'Pago registrado: 28000 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (207, N'2026-10-16T09:10:00.000', 1, N'admin', N'ALTA', N'Pago', 167, N'Pago registrado: 28000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (208, N'2026-10-16T09:20:00.000', 1, N'admin', N'ALTA', N'Pago', 168, N'Pago registrado: 25000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (209, N'2026-10-16T09:30:00.000', 1, N'admin', N'ALTA', N'Pago', 169, N'Pago registrado: 30000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (210, N'2026-10-16T09:40:00.000', 1, N'admin', N'ALTA', N'Pago', 170, N'Pago registrado: 26000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (211, N'2026-10-16T09:50:00.000', 1, N'admin', N'ALTA', N'Pago', 171, N'Pago registrado: 32000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (212, N'2026-10-16T10:00:00.000', 1, N'admin', N'ALTA', N'Pago', 172, N'Pago registrado: 35000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (213, N'2026-10-16T10:10:00.000', 1, N'admin', N'ALTA', N'Pago', 173, N'Pago registrado: 28000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (214, N'2026-10-16T10:20:00.000', 1, N'admin', N'ALTA', N'Pago', 174, N'Pago registrado: 25000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (215, N'2026-10-16T10:30:00.000', 1, N'admin', N'ALTA', N'Pago', 175, N'Pago registrado: 30000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (216, N'2026-10-16T10:40:00.000', 1, N'admin', N'ALTA', N'Pago', 176, N'Pago registrado: 26000.00 - Cliente (Sistema - Medio)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (217, N'2026-10-17T07:08:39.000', 1, N'admin', N'DESCARGA', N'Backup', NULL, N'Backup de la base descargado (backup_newfitnesssystems_2026-10-17.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (218, N'2026-10-17T07:09:28.000', 1, N'admin', N'DESCARGA', N'Backup', NULL, N'Backup de la base descargado (backup_newfitnesssystems_2026-10-17.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (219, N'2026-10-17T07:11:38.000', 1, N'admin', N'DESCARGA', N'Backup', NULL, N'Backup de la base descargado (backup_newfitnesssystems_2026-10-17.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (220, N'2026-10-17T07:15:40.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Falló la importación del backup: Sqlcmd: error: no se pudo abrir o actuar sobre el archivo C:\Users\PC\AppData\Local\Temp\nfs_backup_6ad3209cdf961.sql (motivo: Acceso denegado).');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (221, N'2026-10-17T07:18:53.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Falló la importación del backup: Sqlcmd: error: no se pudo abrir o actuar sobre el archivo C:\Users\PC\AppData\Local\Temp\nfs_backup_6ad3215d3f632.sql (motivo: Acceso denegado).');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (222, N'2026-10-17T07:43:44.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Falló la importación del backup: Sqlcmd: error: no se pudo abrir o actuar sobre el archivo C:\xampp\htdocs\NewFitnessSystems\writable\uploads\nfs_backup_6ad3272fe7b88.sql (motivo: Acceso denegado).');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (223, N'2026-10-17T07:44:46.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Falló la importación del backup: Sqlcmd: error: no se pudo abrir o actuar sobre el archivo C:\xampp\htdocs\NewFitnessSystems\writable\uploads\nfs_backup_6ad3276e52161.sql (motivo: Acceso denegado).');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (225, N'2026-10-17T07:46:54.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Backup importado correctamente (nfs_backup_6ad327ee46f22.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (226, N'2026-10-17T07:48:08.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Falló la importación del backup: Mensaje 208, Nivel 16, Estado 1, Servidor Luci\SQLEXPRESS, Lnea 4
El nombre de objeto ''NoExiste'' no es vlido.');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (228, N'2026-10-17T07:49:15.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Backup importado correctamente (nfs_backup_6ad3287b2deec.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (229, N'2026-10-17T07:49:20.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Falló la importación del backup: Mensaje 208, Nivel 16, Estado 1, Servidor Luci\SQLEXPRESS, Línea 4
El nombre de objeto ''NoExiste'' no es válido.');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (231, N'2026-10-17T07:50:06.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Backup importado correctamente (bk6.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (233, N'2026-10-18T04:06:28.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Backup importado correctamente (nb.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (234, N'2026-10-18T04:08:13.000', 1, N'admin', N'DESCARGA', N'Backup', NULL, N'Backup de la base descargado (backup_newfitnesssystems_2026-10-18.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (235, N'2026-10-18T04:10:10.000', 1, N'admin', N'DESCARGA', N'Backup', NULL, N'Backup de la base descargado (backup_newfitnesssystems_2026-10-18.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (237, N'2026-10-20T19:47:53.000', 1, N'admin', N'IMPORTAR', N'Backup', NULL, N'Backup importado correctamente (full.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (238, N'2026-10-20T19:50:27.000', 1, N'admin', N'DESCARGA', N'Backup', NULL, N'Backup de la base descargado (backup_newfitnesssystems_2026-10-20.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (239, N'2026-10-20T19:51:16.000', 1, N'admin', N'DESCARGA', N'Backup', NULL, N'Backup de la base descargado (backup_newfitnesssystems_2026-10-20.sql)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (240, N'2026-10-20T19:53:01.000', 1, N'admin', N'ALTA', N'Pago', 177, N'Pago de $32.000 registrado (medio #1)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (241, N'2026-10-20T19:53:33.000', 1, N'admin', N'ALTA', N'Pago', 178, N'Pago de $35.000 registrado (medio #2)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (242, N'2026-10-20T20:25:33.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus datos personales');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (243, N'2026-10-20T20:25:47.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Cambió su contraseña');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (244, N'2026-10-20T21:28:50.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (245, N'2026-10-20T21:28:53.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (246, N'2026-10-20T21:28:59.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (247, N'2026-10-20T21:29:38.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (248, N'2026-10-20T21:29:48.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (249, N'2026-10-20T21:29:53.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus datos personales');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (250, N'2026-10-20T21:31:02.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (251, N'2026-10-20T21:31:08.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (252, N'2026-10-20T21:31:33.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (253, N'2026-10-20T21:31:52.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (254, N'2026-10-20T21:33:17.000', 6, N'juanp', N'EDICION', N'Usuario', 6, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (255, N'2026-10-20T21:35:19.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (256, N'2026-10-20T21:35:33.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (257, N'2026-10-20T21:35:47.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (258, N'2026-10-20T21:44:46.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (259, N'2026-10-20T21:45:18.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (260, N'2026-10-20T22:13:07.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (261, N'2026-10-20T22:13:11.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (262, N'2026-10-20T22:13:45.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (263, N'2026-10-20T22:13:51.000', 1, N'admin', N'EDICION', N'Usuario', 1, N'Actualizó sus preferencias');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (264, N'2026-10-20T22:15:06.000', 1, N'admin', N'EDICION', N'Sistema', 5, N'Sistema "Artes Marciales" modificado');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (265, N'2026-10-20T22:36:12.000', 1, N'admin', N'ALTA', N'Cliente', 56, N'Cliente CupoOk, Test (DNI 77777777)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (266, N'2026-10-20T22:53:08.000', 1, N'admin', N'ALTA', N'Cliente', 57, N'Cliente Libre, Test (DNI 66666666)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (267, N'2026-10-21T16:39:16.000', 1, N'admin', N'ALTA', N'Cliente', 56, N'Cliente adfadf, dadfa (DNI 56744999)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (268, N'2026-10-21T17:41:48.000', 1, N'admin', N'ALTA', N'Pago', 180, N'Pago de $32.000 registrado (medio #1)');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (269, N'2026-10-21T17:42:42.000', 1, N'admin', N'BAJA', N'Sistema', 4, N'Sistema dado de baja');
INSERT INTO [dbo].[Bitacora] ([id_log], [fecha], [id_usuario], [nombre_usuario], [accion], [entidad], [id_entidad], [detalle]) VALUES (270, N'2026-10-21T17:42:46.000', 1, N'admin', N'ALTA', N'Sistema', 4, N'Sistema reactivado');
SET IDENTITY_INSERT [dbo].[Bitacora] OFF;
GO

PRINT 'Datos: [dbo].[Comentario] (1 filas)';
SET IDENTITY_INSERT [dbo].[Comentario] ON;
INSERT INTO [dbo].[Comentario] ([id_comentario], [id_persona], [tipo], [mensaje], [fecha_registro], [leido]) VALUES (1, 6, N'queja', N'prueba comentario', N'2026-10-21T02:12:45.600', 0);
SET IDENTITY_INSERT [dbo].[Comentario] OFF;
GO

PRINT 'Datos: [dbo].[Configuracion] (1 filas)';
INSERT INTO [dbo].[Configuracion] ([clave], [valor]) VALUES (N'comision_profesor', N'40');
GO

PRINT 'Datos: [dbo].[Estado_suscripcion] (3 filas)';
SET IDENTITY_INSERT [dbo].[Estado_suscripcion] ON;
INSERT INTO [dbo].[Estado_suscripcion] ([id_estado], [descripcion]) VALUES (1, N'Activa');
INSERT INTO [dbo].[Estado_suscripcion] ([id_estado], [descripcion]) VALUES (2, N'Vencida');
INSERT INTO [dbo].[Estado_suscripcion] ([id_estado], [descripcion]) VALUES (3, N'Cancelada');
SET IDENTITY_INSERT [dbo].[Estado_suscripcion] OFF;
GO

PRINT 'Datos: [dbo].[Gasto] (10 filas)';
SET IDENTITY_INSERT [dbo].[Gasto] ON;
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (1, N'2026-10-01', N'Alquiler', 80000.00, N'Alquiler del local', 1, N'N', N'2026-10-17T02:04:04.067');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (2, N'2026-10-05', N'Sueldos', 150000.00, N'Sueldos profesores', 1, N'N', N'2026-10-17T02:04:04.067');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (3, N'2026-10-10', N'Servicios', 12000.00, N'Luz, agua e internet', 1, N'N', N'2026-10-17T02:04:04.067');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (4, N'2026-10-12', N'Mantenimiento', 8000.00, N'Reparacion de equipos', 1, N'N', N'2026-10-17T02:04:04.067');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (10, N'2026-07-01', N'Alquiler', 80000.00, N'Alquiler del local', 1, N'N', N'2026-10-17T02:45:29.420');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (11, N'2026-08-01', N'Alquiler', 80000.00, N'Alquiler del local', 1, N'N', N'2026-10-17T02:45:29.420');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (12, N'2026-09-01', N'Alquiler', 80000.00, N'Alquiler del local', 1, N'N', N'2026-10-17T02:45:29.420');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (13, N'2026-07-10', N'Servicios', 12000.00, N'Luz, agua e internet', 1, N'N', N'2026-10-17T02:45:29.423');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (14, N'2026-08-10', N'Servicios', 12000.00, N'Luz, agua e internet', 1, N'N', N'2026-10-17T02:45:29.423');
INSERT INTO [dbo].[Gasto] ([id_gasto], [fecha], [categoria], [monto], [detalle], [id_usuario], [baja], [fecha_alta]) VALUES (15, N'2026-09-10', N'Servicios', 12000.00, N'Luz, agua e internet', 1, N'N', N'2026-10-17T02:45:29.423');
SET IDENTITY_INSERT [dbo].[Gasto] OFF;
GO

PRINT 'Datos: [dbo].[Horario] (44 filas)';
SET IDENTITY_INSERT [dbo].[Horario] ON;
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (1, N'08:30:00.0000000', N'09:30:00.0000000', N'Lunes', 1, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (2, N'08:30:00.0000000', N'09:30:00.0000000', N'Martes', 1, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (3, N'08:30:00.0000000', N'09:30:00.0000000', N'Jueves', 1, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (4, N'08:30:00.0000000', N'09:30:00.0000000', N'Viernes', 1, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (5, N'16:00:00.0000000', N'17:00:00.0000000', N'Lunes', 1, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (6, N'16:00:00.0000000', N'17:00:00.0000000', N'Martes', 1, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (7, N'16:00:00.0000000', N'17:00:00.0000000', N'Jueves', 1, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (8, N'16:00:00.0000000', N'17:00:00.0000000', N'Viernes', 1, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (9, N'10:00:00.0000000', N'11:00:00.0000000', N'Lunes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (10, N'10:00:00.0000000', N'11:00:00.0000000', N'Martes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (11, N'10:00:00.0000000', N'11:00:00.0000000', N'Jueves', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (12, N'10:00:00.0000000', N'11:00:00.0000000', N'Viernes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (13, N'14:00:00.0000000', N'15:00:00.0000000', N'Lunes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (14, N'14:00:00.0000000', N'15:00:00.0000000', N'Martes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (15, N'14:00:00.0000000', N'15:00:00.0000000', N'Jueves', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (16, N'14:00:00.0000000', N'15:00:00.0000000', N'Viernes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (17, N'15:00:00.0000000', N'16:00:00.0000000', N'Lunes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (18, N'15:00:00.0000000', N'16:00:00.0000000', N'Martes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (19, N'15:00:00.0000000', N'16:00:00.0000000', N'Jueves', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (20, N'15:00:00.0000000', N'16:00:00.0000000', N'Viernes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (21, N'20:00:00.0000000', N'21:00:00.0000000', N'Miercoles', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (22, N'21:00:00.0000000', N'22:00:00.0000000', N'Martes', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (23, N'21:00:00.0000000', N'22:00:00.0000000', N'Jueves', 2, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (24, N'12:30:00.0000000', N'13:30:00.0000000', N'Lunes', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (25, N'12:30:00.0000000', N'13:30:00.0000000', N'Martes', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (26, N'12:30:00.0000000', N'13:30:00.0000000', N'Miercoles', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (27, N'12:30:00.0000000', N'13:30:00.0000000', N'Jueves', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (28, N'12:30:00.0000000', N'13:30:00.0000000', N'Viernes', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (29, N'18:00:00.0000000', N'19:00:00.0000000', N'Lunes', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (30, N'18:00:00.0000000', N'19:00:00.0000000', N'Martes', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (31, N'18:00:00.0000000', N'19:00:00.0000000', N'Miercoles', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (32, N'18:00:00.0000000', N'19:00:00.0000000', N'Jueves', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (33, N'18:00:00.0000000', N'19:00:00.0000000', N'Viernes', 3, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (34, N'21:00:00.0000000', N'22:00:00.0000000', N'Lunes', 4, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (35, N'21:00:00.0000000', N'22:00:00.0000000', N'Miercoles', 4, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (36, N'21:00:00.0000000', N'22:00:00.0000000', N'Viernes', 4, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (37, N'19:00:00.0000000', N'20:00:00.0000000', N'Lunes', 5, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (38, N'19:00:00.0000000', N'20:00:00.0000000', N'Miercoles', 5, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (39, N'19:00:00.0000000', N'20:00:00.0000000', N'Viernes', 5, N'N', 20);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (40, N'08:00:00.0000000', N'22:00:00.0000000', N'Lunes', 6, N'N', 50);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (41, N'08:00:00.0000000', N'22:00:00.0000000', N'Martes', 6, N'N', 50);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (42, N'08:00:00.0000000', N'22:00:00.0000000', N'Miercoles', 6, N'N', 50);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (43, N'08:00:00.0000000', N'22:00:00.0000000', N'Jueves', 6, N'N', 50);
INSERT INTO [dbo].[Horario] ([id_horario], [hora_inicio], [hora_fin], [dia_semana], [id_sistema], [baja], [cupo_max]) VALUES (44, N'08:00:00.0000000', N'22:00:00.0000000', N'Viernes', 6, N'N', 50);
SET IDENTITY_INSERT [dbo].[Horario] OFF;
GO

PRINT 'Datos: [dbo].[Inscripcion] (61 filas)';
SET IDENTITY_INSERT [dbo].[Inscripcion] ON;
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (1, N'2026-07-01', 6, 1, 1);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (2, N'2026-07-02', 7, 2, 9);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (3, N'2026-07-03', 8, 3, 24);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (4, N'2026-07-04', 9, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (5, N'2026-07-05', 10, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (6, N'2026-07-06', 11, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (7, N'2026-07-07', 12, 1, 5);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (8, N'2026-07-08', 13, 3, 29);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (9, N'2026-07-09', 14, 2, 13);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (10, N'2026-07-10', 15, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (11, N'2026-07-11', 16, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (12, N'2026-07-12', 17, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (13, N'2026-07-13', 18, 1, 1);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (14, N'2026-07-14', 19, 2, 17);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (15, N'2026-07-15', 20, 3, 24);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (16, N'2026-07-16', 21, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (17, N'2026-07-17', 22, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (18, N'2026-07-18', 23, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (19, N'2026-07-19', 24, 1, 5);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (20, N'2026-07-20', 25, 2, 21);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (21, N'2026-07-21', 26, 3, 29);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (22, N'2026-07-22', 27, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (23, N'2026-07-23', 28, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (24, N'2026-07-24', 29, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (25, N'2026-07-25', 30, 1, 1);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (26, N'2026-07-26', 31, 2, 22);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (27, N'2026-07-27', 32, 3, 24);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (28, N'2026-07-28', 33, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (29, N'2026-07-29', 34, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (30, N'2026-07-30', 35, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (31, N'2026-07-31', 36, 1, 5);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (32, N'2026-08-01', 37, 2, 9);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (33, N'2026-08-02', 38, 3, 29);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (34, N'2026-08-03', 39, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (35, N'2026-08-04', 40, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (36, N'2026-08-05', 41, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (37, N'2026-08-06', 42, 1, 1);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (38, N'2026-08-07', 43, 2, 13);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (39, N'2026-08-08', 44, 3, 24);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (40, N'2026-08-09', 45, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (41, N'2026-08-10', 46, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (42, N'2026-08-11', 47, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (43, N'2026-08-12', 48, 1, 5);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (44, N'2026-08-13', 49, 2, 17);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (45, N'2026-08-14', 50, 3, 29);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (46, N'2026-08-15', 51, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (47, N'2026-08-16', 52, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (48, N'2026-08-17', 53, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (49, N'2026-08-18', 54, 1, 1);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (50, N'2026-08-19', 55, 2, 21);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (51, N'2026-08-20', 6, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (52, N'2026-08-21', 7, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (53, N'2026-08-22', 8, 1, 5);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (54, N'2026-08-23', 9, 2, 22);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (55, N'2026-08-24', 10, 3, 24);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (56, N'2026-08-25', 11, 4, 34);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (57, N'2026-08-26', 12, 5, 37);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (58, N'2026-08-27', 13, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (59, N'2026-08-28', 14, 1, 1);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (60, N'2026-08-29', 15, 6, NULL);
INSERT INTO [dbo].[Inscripcion] ([id_inscripcion], [fecha_inscripcion], [id_persona], [id_sistema], [id_horario]) VALUES (61, N'2026-10-21', 56, 6, NULL);
SET IDENTITY_INSERT [dbo].[Inscripcion] OFF;
GO

PRINT 'Datos: [dbo].[Medio_pago] (2 filas)';
SET IDENTITY_INSERT [dbo].[Medio_pago] ON;
INSERT INTO [dbo].[Medio_pago] ([id_medio_pago], [descripcion]) VALUES (1, N'Efectivo');
INSERT INTO [dbo].[Medio_pago] ([id_medio_pago], [descripcion]) VALUES (2, N'Transferencia');
SET IDENTITY_INSERT [dbo].[Medio_pago] OFF;
GO

PRINT 'Datos: [dbo].[Mensualidad] (6 filas)';
SET IDENTITY_INSERT [dbo].[Mensualidad] ON;
INSERT INTO [dbo].[Mensualidad] ([id_mensualidad], [precio], [fecha_vigencia], [id_sistema]) VALUES (1, 28000.00, N'2026-07-01', 1);
INSERT INTO [dbo].[Mensualidad] ([id_mensualidad], [precio], [fecha_vigencia], [id_sistema]) VALUES (2, 25000.00, N'2026-07-01', 2);
INSERT INTO [dbo].[Mensualidad] ([id_mensualidad], [precio], [fecha_vigencia], [id_sistema]) VALUES (3, 30000.00, N'2026-07-01', 3);
INSERT INTO [dbo].[Mensualidad] ([id_mensualidad], [precio], [fecha_vigencia], [id_sistema]) VALUES (4, 26000.00, N'2026-07-01', 4);
INSERT INTO [dbo].[Mensualidad] ([id_mensualidad], [precio], [fecha_vigencia], [id_sistema]) VALUES (5, 32000.00, N'2026-07-01', 5);
INSERT INTO [dbo].[Mensualidad] ([id_mensualidad], [precio], [fecha_vigencia], [id_sistema]) VALUES (6, 35000.00, N'2026-07-01', 6);
SET IDENTITY_INSERT [dbo].[Mensualidad] OFF;
GO

PRINT 'Datos: [dbo].[MotivoBaja] (1 filas)';
SET IDENTITY_INSERT [dbo].[MotivoBaja] ON;
INSERT INTO [dbo].[MotivoBaja] ([id_motivo_baja], [id_inscripcion], [id_persona], [motivo], [fecha_registro]) VALUES (2, 1, 6, N'prueba', N'2026-10-21T02:11:51.903');
SET IDENTITY_INSERT [dbo].[MotivoBaja] OFF;
GO

PRINT 'Datos: [dbo].[Pago] (162 filas)';
SET IDENTITY_INSERT [dbo].[Pago] ON;
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (1, N'2026-07-01', 28000.00, 1, 1, 2, 1);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (3, N'2026-07-03', 30000.00, 1, 3, 4, 3);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (4, N'2026-07-04', 26000.00, 2, 4, 5, 4);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (5, N'2026-07-05', 32000.00, 1, 5, 2, 5);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (6, N'2026-07-06', 35000.00, 2, 6, 3, 6);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (7, N'2026-07-07', 28000.00, 1, 1, 4, 7);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (8, N'2026-07-08', 30000.00, 2, 3, 5, 8);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (9, N'2026-07-09', 25000.00, 1, 2, 2, 9);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (10, N'2026-07-10', 26000.00, 2, 4, 3, 10);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (11, N'2026-07-11', 32000.00, 1, 5, 4, 11);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (12, N'2026-07-12', 35000.00, 2, 6, 5, 12);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (13, N'2026-08-12', 35000.00, 1, 6, 2, 12);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (14, N'2026-09-12', 35000.00, 2, 6, 3, 12);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (15, N'2026-10-12', 35000.00, 1, 6, 4, 12);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (16, N'2026-07-13', 28000.00, 2, 1, 5, 13);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (17, N'2026-08-13', 28000.00, 1, 1, 2, 13);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (18, N'2026-09-13', 28000.00, 2, 1, 3, 13);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (19, N'2026-10-13', 28000.00, 1, 1, 4, 13);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (20, N'2026-07-14', 25000.00, 2, 2, 5, 14);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (21, N'2026-08-14', 25000.00, 1, 2, 2, 14);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (22, N'2026-09-14', 25000.00, 2, 2, 3, 14);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (23, N'2026-10-14', 25000.00, 1, 2, 4, 14);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (24, N'2026-07-15', 30000.00, 2, 3, 5, 15);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (25, N'2026-08-15', 30000.00, 1, 3, 2, 15);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (26, N'2026-09-15', 30000.00, 2, 3, 3, 15);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (27, N'2026-10-15', 30000.00, 1, 3, 4, 15);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (28, N'2026-07-16', 26000.00, 2, 4, 5, 16);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (29, N'2026-08-16', 26000.00, 1, 4, 2, 16);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (30, N'2026-09-16', 26000.00, 2, 4, 3, 16);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (31, N'2026-10-16', 26000.00, 1, 4, 4, 16);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (32, N'2026-07-17', 32000.00, 2, 5, 5, 17);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (33, N'2026-08-17', 32000.00, 1, 5, 2, 17);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (34, N'2026-09-17', 32000.00, 2, 5, 3, 17);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (35, N'2026-10-17', 32000.00, 1, 5, 4, 17);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (36, N'2026-07-18', 35000.00, 2, 6, 5, 18);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (37, N'2026-08-18', 35000.00, 1, 6, 2, 18);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (38, N'2026-09-18', 35000.00, 2, 6, 3, 18);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (39, N'2026-07-19', 28000.00, 1, 1, 4, 19);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (40, N'2026-08-19', 28000.00, 2, 1, 5, 19);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (41, N'2026-09-19', 28000.00, 1, 1, 2, 19);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (42, N'2026-07-20', 25000.00, 2, 2, 3, 20);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (43, N'2026-08-20', 25000.00, 1, 2, 4, 20);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (44, N'2026-09-20', 25000.00, 2, 2, 5, 20);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (45, N'2026-07-21', 30000.00, 1, 3, 2, 21);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (46, N'2026-08-21', 30000.00, 2, 3, 3, 21);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (47, N'2026-09-21', 30000.00, 1, 3, 4, 21);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (48, N'2026-07-22', 26000.00, 2, 4, 5, 22);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (49, N'2026-08-22', 26000.00, 1, 4, 2, 22);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (50, N'2026-09-22', 26000.00, 2, 4, 3, 22);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (51, N'2026-07-23', 32000.00, 1, 5, 4, 23);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (52, N'2026-08-23', 32000.00, 2, 5, 5, 23);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (53, N'2026-09-23', 32000.00, 1, 5, 2, 23);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (54, N'2026-07-24', 35000.00, 2, 6, 3, 24);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (55, N'2026-08-24', 35000.00, 1, 6, 4, 24);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (56, N'2026-09-24', 35000.00, 2, 6, 5, 24);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (57, N'2026-07-25', 28000.00, 1, 1, 2, 25);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (58, N'2026-08-25', 28000.00, 2, 1, 3, 25);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (59, N'2026-09-25', 28000.00, 1, 1, 4, 25);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (60, N'2026-07-26', 25000.00, 2, 2, 5, 26);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (61, N'2026-08-26', 25000.00, 1, 2, 2, 26);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (62, N'2026-09-26', 25000.00, 2, 2, 3, 26);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (63, N'2026-07-27', 30000.00, 1, 3, 4, 27);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (64, N'2026-08-27', 30000.00, 2, 3, 5, 27);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (65, N'2026-09-27', 30000.00, 1, 3, 2, 27);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (66, N'2026-07-28', 26000.00, 2, 4, 3, 28);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (67, N'2026-08-28', 26000.00, 1, 4, 4, 28);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (68, N'2026-09-28', 26000.00, 2, 4, 5, 28);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (69, N'2026-07-29', 32000.00, 1, 5, 2, 29);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (70, N'2026-08-29', 32000.00, 2, 5, 3, 29);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (71, N'2026-09-29', 32000.00, 1, 5, 4, 29);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (72, N'2026-07-30', 35000.00, 2, 6, 5, 30);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (73, N'2026-08-30', 35000.00, 1, 6, 2, 30);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (74, N'2026-09-30', 35000.00, 2, 6, 3, 30);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (75, N'2026-07-31', 28000.00, 1, 1, 4, 31);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (76, N'2026-08-31', 28000.00, 2, 1, 5, 31);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (77, N'2026-10-01', 28000.00, 1, 1, 2, 31);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (78, N'2026-08-01', 25000.00, 2, 2, 3, 32);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (79, N'2026-09-01', 25000.00, 1, 2, 4, 32);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (80, N'2026-10-01', 25000.00, 2, 2, 5, 32);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (81, N'2026-08-02', 30000.00, 1, 3, 2, 33);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (82, N'2026-09-02', 30000.00, 2, 3, 3, 33);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (83, N'2026-10-02', 30000.00, 1, 3, 4, 33);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (84, N'2026-08-03', 26000.00, 2, 4, 5, 34);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (85, N'2026-09-03', 26000.00, 1, 4, 2, 34);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (86, N'2026-10-03', 26000.00, 2, 4, 3, 34);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (87, N'2026-08-04', 32000.00, 1, 5, 4, 35);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (88, N'2026-09-04', 32000.00, 2, 5, 5, 35);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (89, N'2026-10-04', 32000.00, 1, 5, 2, 35);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (90, N'2026-08-05', 35000.00, 2, 6, 3, 36);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (91, N'2026-09-05', 35000.00, 1, 6, 4, 36);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (92, N'2026-10-05', 35000.00, 2, 6, 5, 36);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (93, N'2026-08-06', 28000.00, 1, 1, 2, 37);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (94, N'2026-09-06', 28000.00, 2, 1, 3, 37);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (95, N'2026-10-06', 28000.00, 1, 1, 4, 37);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (96, N'2026-08-07', 25000.00, 2, 2, 5, 38);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (97, N'2026-09-07', 25000.00, 1, 2, 2, 38);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (98, N'2026-10-07', 25000.00, 2, 2, 3, 38);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (99, N'2026-08-08', 30000.00, 1, 3, 4, 39);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (100, N'2026-09-08', 30000.00, 2, 3, 5, 39);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (101, N'2026-10-08', 30000.00, 1, 3, 2, 39);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (102, N'2026-08-09', 26000.00, 2, 4, 3, 40);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (103, N'2026-09-09', 26000.00, 1, 4, 4, 40);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (104, N'2026-10-09', 26000.00, 2, 4, 5, 40);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (105, N'2026-08-10', 32000.00, 1, 5, 2, 41);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (106, N'2026-09-10', 32000.00, 2, 5, 3, 41);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (107, N'2026-10-10', 32000.00, 1, 5, 4, 41);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (108, N'2026-08-11', 35000.00, 2, 6, 5, 42);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (109, N'2026-09-11', 35000.00, 1, 6, 2, 42);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (110, N'2026-10-11', 35000.00, 2, 6, 3, 42);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (111, N'2026-08-12', 28000.00, 1, 1, 4, 43);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (112, N'2026-09-12', 28000.00, 2, 1, 5, 43);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (113, N'2026-10-12', 28000.00, 1, 1, 2, 43);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (114, N'2026-08-13', 25000.00, 2, 2, 3, 44);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (115, N'2026-09-13', 25000.00, 1, 2, 4, 44);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (116, N'2026-10-13', 25000.00, 2, 2, 5, 44);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (117, N'2026-08-14', 30000.00, 1, 3, 2, 45);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (118, N'2026-09-14', 30000.00, 2, 3, 3, 45);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (119, N'2026-10-14', 30000.00, 1, 3, 4, 45);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (120, N'2026-08-15', 26000.00, 2, 4, 5, 46);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (121, N'2026-09-15', 26000.00, 1, 4, 2, 46);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (122, N'2026-10-15', 26000.00, 2, 4, 3, 46);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (123, N'2026-08-16', 32000.00, 1, 5, 4, 47);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (124, N'2026-09-16', 32000.00, 2, 5, 5, 47);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (125, N'2026-10-16', 32000.00, 1, 5, 2, 47);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (126, N'2026-08-17', 35000.00, 2, 6, 3, 48);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (127, N'2026-09-17', 35000.00, 1, 6, 4, 48);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (128, N'2026-10-17', 35000.00, 2, 6, 5, 48);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (129, N'2026-08-18', 28000.00, 1, 1, 2, 49);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (130, N'2026-09-18', 28000.00, 2, 1, 3, 49);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (131, N'2026-08-19', 25000.00, 1, 2, 4, 50);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (132, N'2026-09-19', 25000.00, 2, 2, 5, 50);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (133, N'2026-08-20', 35000.00, 1, 6, 2, 51);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (134, N'2026-09-20', 35000.00, 2, 6, 3, 51);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (137, N'2026-08-22', 28000.00, 1, 1, 2, 53);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (138, N'2026-09-22', 28000.00, 2, 1, 3, 53);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (139, N'2026-08-23', 25000.00, 1, 2, 4, 54);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (140, N'2026-09-23', 25000.00, 2, 2, 5, 54);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (141, N'2026-08-24', 30000.00, 1, 3, 2, 55);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (142, N'2026-09-24', 30000.00, 2, 3, 3, 55);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (143, N'2026-08-25', 26000.00, 1, 4, 4, 56);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (144, N'2026-09-25', 26000.00, 2, 4, 5, 56);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (145, N'2026-08-26', 32000.00, 1, 5, 2, 57);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (146, N'2026-09-26', 32000.00, 2, 5, 3, 57);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (147, N'2026-08-27', 35000.00, 1, 6, 4, 58);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (148, N'2026-09-27', 35000.00, 2, 6, 5, 58);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (149, N'2026-08-28', 28000.00, 1, 1, 2, 59);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (150, N'2026-09-28', 28000.00, 2, 1, 3, 59);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (151, N'2026-08-29', 35000.00, 1, 6, 4, 60);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (152, N'2026-09-29', 35000.00, 2, 6, 5, 60);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (167, N'2026-10-16', 28000.00, 1, 1, 1, 1);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (169, N'2026-10-16', 30000.00, 1, 3, 3, 3);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (170, N'2026-10-16', 26000.00, 2, 4, 4, 4);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (171, N'2026-10-16', 32000.00, 1, 5, 5, 5);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (172, N'2026-10-16', 35000.00, 2, 6, 6, 6);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (173, N'2026-10-16', 28000.00, 1, 1, 1, 7);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (174, N'2026-10-16', 25000.00, 2, 2, 2, 8);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (175, N'2026-10-16', 30000.00, 1, 3, 3, 9);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (176, N'2026-10-16', 26000.00, 2, 4, 4, 10);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (177, N'2026-10-20', 32000.00, 1, 5, 1, 5);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (178, N'2026-10-20', 35000.00, 2, 6, 1, 6);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (179, N'2026-10-21', 35000.00, 1, 6, 1, 61);
INSERT INTO [dbo].[Pago] ([id_pago], [fecha_pago], [monto], [id_medio_pago], [id_mensualidad], [cobrado_por], [id_suscripcion]) VALUES (180, N'2026-10-21', 32000.00, 1, 5, 1, 41);
SET IDENTITY_INSERT [dbo].[Pago] OFF;
GO

PRINT 'Datos: [dbo].[Persona] (56 filas)';
SET IDENTITY_INSERT [dbo].[Persona] ON;
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (1, N'Luciana', N'Admin', N'admin@newfitness.com', N'3794000001', N'40000001', 1, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (2, N'Carlos', N'Gomez', N'carlos.gomez@newfitness.com', N'3794000002', N'40000002', 2, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (3, N'Mariana', N'Ruiz', N'mariana.ruiz@newfitness.com', N'3794000003', N'40000003', 2, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (4, N'Federico', N'Sosa', N'federico.sosa@newfitness.com', N'3794000004', N'40000004', 2, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (5, N'Camila', N'Torres', N'camila.torres@newfitness.com', N'3794000005', N'40000005', 2, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (6, N'Juan', N'Perez', N'juan.perez@example.com', N'3794000006', N'40000006', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (7, N'Maria', N'Lopez', N'maria.lopez@example.com', N'3794000007', N'40000007', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (8, N'Ana', N'Fernandez', N'ana.fernandez@example.com', N'3794000008', N'40000008', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (9, N'Diego', N'Ramirez', N'diego.ramirez@example.com', N'3794000009', N'40000009', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (10, N'Sofia', N'Acosta', N'sofia.acosta@example.com', N'3794000010', N'40000010', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (11, N'Nicolas', N'Benitez', N'nicolas.benitez@example.com', N'3794000011', N'40000011', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (12, N'Valentina', N'Molina', N'valentina.molina@example.com', N'3794000012', N'40000012', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (13, N'Tomas', N'Silva', N'tomas.silva@example.com', N'3794000013', N'40000013', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (14, N'Martina', N'Gimenez', N'martina.gimenez@example.com', N'3794000014', N'40000014', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (15, N'Lucas', N'Castro', N'lucas.castro@example.com', N'3794000015', N'40000015', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (16, N'Florencia', N'Rojas', N'florencia.rojas@example.com', N'3794000016', N'40000016', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (17, N'Mateo', N'Herrera', N'mateo.herrera@example.com', N'3794000017', N'40000017', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (18, N'Julieta', N'Vera', N'julieta.vera@example.com', N'3794000018', N'40000018', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (19, N'Santiago', N'Aguirre', N'santiago.aguirre@example.com', N'3794000019', N'40000019', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (20, N'Carla', N'Medina', N'carla.medina@example.com', N'3794000020', N'40000020', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (21, N'Bruno', N'Luna', N'bruno.luna@example.com', N'3794000021', N'40000021', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (22, N'Micaela', N'Ortiz', N'micaela.ortiz@example.com', N'3794000022', N'40000022', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (23, N'Ezequiel', N'Navarro', N'ezequiel.navarro@example.com', N'3794000023', N'40000023', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (24, N'Lucia', N'Paredes', N'lucia.paredes@example.com', N'3794000024', N'40000024', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (25, N'Franco', N'Caceres', N'franco.caceres@example.com', N'3794000025', N'40000025', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (26, N'Rocio', N'Morales', N'rocio.morales@example.com', N'3794000026', N'40000026', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (27, N'Emiliano', N'Paz', N'emiliano.paz@example.com', N'3794000027', N'40000027', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (28, N'Abril', N'Romero', N'abril.romero@example.com', N'3794000028', N'40000028', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (29, N'Gonzalo', N'Ibarra', N'gonzalo.ibarra@example.com', N'3794000029', N'40000029', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (30, N'Bianca', N'Vargas', N'bianca.vargas@example.com', N'3794000030', N'40000030', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (31, N'Matias', N'Peralta', N'matias.peralta@example.com', N'3794000031', N'40000031', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (32, N'Aldana', N'Correa', N'aldana.correa@example.com', N'3794000032', N'40000032', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (33, N'Leandro', N'Mansilla', N'leandro.mansilla@example.com', N'3794000033', N'40000033', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (34, N'Milagros', N'Cano', N'milagros.cano@example.com', N'3794000034', N'40000034', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (35, N'Joaquin', N'Farias', N'joaquin.farias@example.com', N'3794000035', N'40000035', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (36, N'Pilar', N'Dominguez', N'pilar.dominguez@example.com', N'3794000036', N'40000036', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (37, N'Lautaro', N'Sanchez', N'lautaro.sanchez@example.com', N'3794000037', N'40000037', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (38, N'Delfina', N'Nuñez', N'delfina.nunez@example.com', N'3794000038', N'40000038', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (39, N'Ramiro', N'Rios', N'ramiro.rios@example.com', N'3794000039', N'40000039', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (40, N'Candela', N'Soto', N'candela.soto@example.com', N'3794000040', N'40000040', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (41, N'Ivan', N'Arias', N'ivan.arias@example.com', N'3794000041', N'40000041', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (42, N'Natalia', N'Ferreyra', N'natalia.ferreyra@example.com', N'3794000042', N'40000042', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (43, N'Maximiliano', N'Godoy', N'maximiliano.godoy@example.com', N'3794000043', N'40000043', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (44, N'Antonella', N'Suarez', N'antonella.suarez@example.com', N'3794000044', N'40000044', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (45, N'Sebastian', N'Cruz', N'sebastian.cruz@example.com', N'3794000045', N'40000045', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (46, N'Jazmin', N'Duarte', N'jazmin.duarte@example.com', N'3794000046', N'40000046', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (47, N'Thiago', N'Aguilera', N'thiago.aguilera@example.com', N'3794000047', N'40000047', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (48, N'Renata', N'Cabrera', N'renata.cabrera@example.com', N'3794000048', N'40000048', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (49, N'Agustin', N'Roldan', N'agustin.roldan@example.com', N'3794000049', N'40000049', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (50, N'Noelia', N'Blanco', N'noelia.blanco@example.com', N'3794000050', N'40000050', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (51, N'Bautista', N'Escobar', N'bautista.escobar@example.com', N'3794000051', N'40000051', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (52, N'Mora', N'Guzman', N'mora.guzman@example.com', N'3794000052', N'40000052', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (53, N'Santino', N'Vega', N'santino.vega@example.com', N'3794000053', N'40000053', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (54, N'Victoria', N'Maidana', N'victoria.maidana@example.com', N'3794000054', N'40000054', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (55, N'Gael', N'Quiroga', N'gael.quiroga@example.com', N'3794000055', N'40000055', 3, N'N');
INSERT INTO [dbo].[Persona] ([id_persona], [nombre], [apellido], [email], [telefono], [dni], [id_rol], [baja]) VALUES (56, N'dadfa', N'adfadf', N'dafadf@example.com', N'53534534', N'56744999', 3, N'N');
SET IDENTITY_INSERT [dbo].[Persona] OFF;
GO

PRINT 'Datos: [dbo].[PlantillaMensaje] (6 filas)';
SET IDENTITY_INSERT [dbo].[PlantillaMensaje] ON;
INSERT INTO [dbo].[PlantillaMensaje] ([id_plantilla], [titulo], [categoria], [mensaje], [baja]) VALUES (1, N'Recordatorio de vencimiento', N'Vencimiento', N'Hola {nombre}! Te recordamos que tu cuota vence el {vencimiento}. Podés renovarla en recepción para seguir entrenando sin interrupciones. Cualquier consulta, escribinos!', 0);
INSERT INTO [dbo].[PlantillaMensaje] ([id_plantilla], [titulo], [categoria], [mensaje], [baja]) VALUES (2, N'Cuota vencida', N'Vencimiento', N'Hola {nombre}! Notamos que tu cuota venció el {vencimiento}. Pasá por recepción o escribinos para regularizarla y seguir con tu rutina. Te esperamos!', 0);
INSERT INTO [dbo].[PlantillaMensaje] ([id_plantilla], [titulo], [categoria], [mensaje], [baja]) VALUES (3, N'Promo del mes', N'Promo', N'Hola {nombre}! Este mes tenemos una promo especial para vos: 2x1 en la cuota. Aprovechá y seguí entrenando con nosotros!', 0);
INSERT INTO [dbo].[PlantillaMensaje] ([id_plantilla], [titulo], [categoria], [mensaje], [baja]) VALUES (4, N'Descuento por tiempo', N'Promo', N'Hola {nombre}! Por ser parte de la familia del gym, te ofrecemos 25% de descuento en tu próxima cuota. Aplicá antes de fin de mes!', 0);
INSERT INTO [dbo].[PlantillaMensaje] ([id_plantilla], [titulo], [categoria], [mensaje], [baja]) VALUES (5, N'Te extrañamos', N'Retención', N'Hola {nombre}! Hace {dias} días que no te vemos por el gym y te extrañamos. Tu cuota está al día, así que podés volver cuando quieras. Te esperamos!', 0);
INSERT INTO [dbo].[PlantillaMensaje] ([id_plantilla], [titulo], [categoria], [mensaje], [baja]) VALUES (6, N'Aviso general', N'Aviso', N'Hola {nombre}! Te avisamos que {motivo}. Disculpá las molestias. Cualquier duda, escribinos!', 0);
SET IDENTITY_INSERT [dbo].[PlantillaMensaje] OFF;
GO

PRINT 'Datos: [dbo].[Preferencia_usuario] (4 filas)';
SET IDENTITY_INSERT [dbo].[Preferencia_usuario] ON;
INSERT INTO [dbo].[Preferencia_usuario] ([id_preferencia], [id_usuario], [tema], [notif_email], [notif_dias]) VALUES (1, 6, N'dark', 1, 7);
INSERT INTO [dbo].[Preferencia_usuario] ([id_preferencia], [id_usuario], [tema], [notif_email], [notif_dias]) VALUES (2, 1, N'dark', 1, 1);
INSERT INTO [dbo].[Preferencia_usuario] ([id_preferencia], [id_usuario], [tema], [notif_email], [notif_dias]) VALUES (3, 2, N'dark', 1, 7);
INSERT INTO [dbo].[Preferencia_usuario] ([id_preferencia], [id_usuario], [tema], [notif_email], [notif_dias]) VALUES (4, 4, N'dark', 1, 7);
SET IDENTITY_INSERT [dbo].[Preferencia_usuario] OFF;
GO

PRINT 'Datos: [dbo].[Reserva] (44 filas)';
SET IDENTITY_INSERT [dbo].[Reserva] ON;
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (1, 1, 6, N'2026-10-13', N'RESERVADA', 6, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (2, 5, 7, N'2026-10-14', N'RESERVADA', 7, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (3, 9, 8, N'2026-10-15', N'LISTA_ESPERA', 8, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (4, 13, 9, N'2026-10-16', N'RESERVADA', 9, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (5, 17, 10, N'2026-10-17', N'CANCELADA', 10, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (6, 21, 11, N'2026-10-20', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (7, 24, 12, N'2026-10-21', N'LISTA_ESPERA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (8, 28, 13, N'2026-10-22', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (9, 31, 15, N'2026-10-23', N'RESERVADA', 11, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (10, 34, 16, N'2026-10-13', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (11, 37, 18, N'2026-10-14', N'LISTA_ESPERA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (12, 40, 20, N'2026-10-15', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (13, 1, 22, N'2026-10-16', N'CANCELADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (14, 5, 25, N'2026-10-17', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (15, 9, 28, N'2026-10-20', N'LISTA_ESPERA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (16, 13, 30, N'2026-10-21', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (17, 17, 34, N'2026-10-22', N'RESERVADA', 12, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (18, 21, 40, N'2026-10-23', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (19, 24, 45, N'2026-10-13', N'LISTA_ESPERA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (20, 28, 50, N'2026-10-14', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (21, 31, 6, N'2026-10-15', N'CANCELADA', 6, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (22, 34, 7, N'2026-10-16', N'RESERVADA', 7, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (23, 37, 8, N'2026-10-17', N'LISTA_ESPERA', 8, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (24, 40, 9, N'2026-10-20', N'RESERVADA', 9, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (25, 1, 10, N'2026-10-21', N'RESERVADA', 10, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (26, 5, 11, N'2026-10-22', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (27, 9, 12, N'2026-10-23', N'LISTA_ESPERA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (28, 13, 13, N'2026-10-13', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (29, 17, 15, N'2026-10-14', N'CANCELADA', 11, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (30, 21, 16, N'2026-10-15', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (31, 24, 18, N'2026-10-16', N'LISTA_ESPERA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (32, 28, 20, N'2026-10-17', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (33, 31, 22, N'2026-10-20', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (34, 34, 25, N'2026-10-21', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (35, 37, 28, N'2026-10-22', N'LISTA_ESPERA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (36, 40, 30, N'2026-10-23', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (37, 1, 34, N'2026-10-13', N'CANCELADA', 12, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (38, 5, 40, N'2026-10-14', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (39, 9, 45, N'2026-10-15', N'LISTA_ESPERA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (40, 13, 50, N'2026-10-16', N'RESERVADA', NULL, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (41, 17, 6, N'2026-10-17', N'RESERVADA', 6, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (42, 21, 7, N'2026-10-20', N'RESERVADA', 7, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (43, 24, 8, N'2026-10-21', N'LISTA_ESPERA', 8, NULL, N'2026-10-17T00:49:41.300');
INSERT INTO [dbo].[Reserva] ([id_reserva], [id_horario], [id_persona], [fecha], [estado], [id_usuario_inscripcion], [observacion], [fecha_creacion]) VALUES (44, 28, 9, N'2026-10-22', N'RESERVADA', 9, NULL, N'2026-10-17T00:49:41.300');
SET IDENTITY_INSERT [dbo].[Reserva] OFF;
GO

PRINT 'Datos: [dbo].[Rol] (3 filas)';
SET IDENTITY_INSERT [dbo].[Rol] ON;
INSERT INTO [dbo].[Rol] ([id_rol], [descripcion]) VALUES (1, N'Administrador');
INSERT INTO [dbo].[Rol] ([id_rol], [descripcion]) VALUES (2, N'Profesor');
INSERT INTO [dbo].[Rol] ([id_rol], [descripcion]) VALUES (3, N'Cliente');
SET IDENTITY_INSERT [dbo].[Rol] OFF;
GO

PRINT 'Datos: [dbo].[Rutina] (11 filas)';
SET IDENTITY_INSERT [dbo].[Rutina] ON;
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (1, 2, NULL, N'Full Body - General', N'Rutina general para todos los socios', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 1);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (2, 3, NULL, N'Cardio + Core', N'Rutina general cardiovascular', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 1);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (3, 4, NULL, N'Principiantes - Adaptación', N'Rutina general para principiantes', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 1);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (4, 5, NULL, N'Piernas y Glúteos', N'Rutina general de tren inferior', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 1);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (5, 2, 6, N'Espalda y Bíceps (Juan)', N'Rutina personalizada', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 0);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (6, 3, 7, N'Torso (María)', N'Rutina personalizada', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 0);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (7, 4, 15, N'Hipertrofia (Lucas)', N'Rutina personalizada', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 0);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (8, 5, 34, N'Tonificación (Milagros)', N'Rutina personalizada', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 0);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (9, 2, 8, N'Cardio (Ana)', N'Rutina personalizada', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 0);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (10, 3, 9, N'Fuerza (Diego)', N'Rutina personalizada', N'2026-08-01', N'N', N'2026-10-17T00:49:41.280', 0);
INSERT INTO [dbo].[Rutina] ([id_rutina], [id_profesor], [id_cliente], [nombre], [descripcion], [fecha], [baja], [fecha_creacion], [para_todos]) VALUES (11, 1, NULL, N'sgsgs', N'sdgdsvsd', N'2026-10-21', N'N', N'2026-10-21T17:43:22.177', 1);
SET IDENTITY_INSERT [dbo].[Rutina] OFF;
GO

PRINT 'Datos: [dbo].[RutinaDia] (23 filas)';
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (1, 1);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (1, 3);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (1, 5);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (2, 2);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (2, 4);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (3, 1);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (3, 2);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (3, 3);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (3, 4);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (3, 5);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (4, 2);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (4, 4);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (5, 1);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (6, 3);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (7, 1);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (7, 3);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (7, 5);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (8, 2);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (8, 4);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (9, 6);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (10, 1);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (10, 4);
INSERT INTO [dbo].[RutinaDia] ([id_rutina], [dia_semana]) VALUES (11, 1);
GO

PRINT 'Datos: [dbo].[RutinaEjercicio] (44 filas)';
SET IDENTITY_INSERT [dbo].[RutinaEjercicio] ON;
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (6, 2, N'Cinta (HIIT)', N'1', N'20 min', N'-', N'-', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (7, 2, N'Burpees', N'3', N'15', N'-', N'40s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (8, 2, N'Plancha', N'3', N'40s', N'-', N'30s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (9, 2, N'Mountain climbers', N'3', N'20', N'-', N'30s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (10, 3, N'Movilidad articular', N'1', N'10 min', N'-', N'-', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (11, 3, N'Prensa de piernas', N'3', N'12', N'50 kg', N'60s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (12, 3, N'Remo en polea', N'3', N'12', N'25 kg', N'60s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (13, 3, N'Peso muerto rumano', N'3', N'10', N'30 kg', N'60s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (14, 3, N'Plancha', N'3', N'30s', N'-', N'30s', 4, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (15, 4, N'Hip thrust', N'4', N'12', N'60 kg', N'60s', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (16, 4, N'Sentadilla sumo', N'4', N'12', N'35 kg', N'60s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (17, 4, N'Zancadas', N'3', N'12', N'16 kg', N'60s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (18, 4, N'Gemelos', N'4', N'20', N'40 kg', N'30s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (19, 4, N'Abductores', N'3', N'15', N'30 kg', N'30s', 4, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (20, 5, N'Dominadas', N'4', N'8', N'-', N'60s', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (21, 5, N'Remo con barra', N'4', N'10', N'40 kg', N'60s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (22, 5, N'Curl de bíceps', N'3', N'12', N'14 kg', N'45s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (23, 5, N'Curl martillo', N'3', N'12', N'12 kg', N'45s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (24, 6, N'Press inclinado', N'4', N'10', N'25 kg', N'60s', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (25, 6, N'Aperturas', N'3', N'12', N'10 kg', N'45s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (26, 6, N'Fondos', N'3', N'10', N'-', N'60s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (27, 6, N'Tríceps polea', N'3', N'15', N'20 kg', N'45s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (28, 7, N'Press militar', N'4', N'10', N'30 kg', N'60s', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (29, 7, N'Elevaciones laterales', N'3', N'15', N'8 kg', N'45s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (30, 7, N'Remo en polea', N'4', N'12', N'35 kg', N'60s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (31, 7, N'Bíceps mancuerna', N'3', N'12', N'12 kg', N'45s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (32, 8, N'Estocadas', N'3', N'12', N'-', N'45s', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (33, 8, N'Glúteo en polea', N'4', N'12', N'15 kg', N'45s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (34, 8, N'Abductores', N'3', N'15', N'25 kg', N'30s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (35, 8, N'Plancha lateral', N'3', N'30s', N'-', N'30s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (36, 9, N'Bicicleta fija', N'1', N'30 min', N'-', N'-', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (37, 9, N'Saltos de cuerda', N'4', N'1 min', N'-', N'60s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (38, 9, N'Burpees', N'3', N'12', N'-', N'45s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (39, 9, N'Escalador', N'3', N'20', N'-', N'30s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (40, 10, N'Sentadilla', N'5', N'5', N'70 kg', N'120s', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (41, 10, N'Peso muerto', N'4', N'6', N'80 kg', N'120s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (42, 10, N'Press banca', N'4', N'6', N'50 kg', N'120s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (43, 10, N'Remo con barra', N'4', N'8', N'45 kg', N'90s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (45, 1, N'Sentadilla con barra', N'4', N'12', N'40 kg', N'60s', 0, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (46, 1, N'Press de banca', N'4', N'10', N'30 kg', N'60s', 1, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (47, 1, N'Remo con barra', N'4', N'12', N'35 kg', N'60s', 2, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (48, 1, N'Curl de bíceps', N'3', N'15', N'12 kg', N'45s', 3, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (49, 1, N'Abdominales', N'3', N'20', N'-', N'30s', 4, NULL);
INSERT INTO [dbo].[RutinaEjercicio] ([id_ejercicio], [id_rutina], [nombre], [series], [repeticiones], [peso], [descanso], [orden], [observacion]) VALUES (50, 11, N'dgsdgs', N'50', N'4', N'40', N'4', 0, N'dsdgsg');
SET IDENTITY_INSERT [dbo].[RutinaEjercicio] OFF;
GO

PRINT 'Datos: [dbo].[SeguimientoFisico] (120 filas)';
SET IDENTITY_INSERT [dbo].[SeguimientoFisico] ON;
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (1, 6, N'2026-08-01', 62.00, 25.00, 86.00, 101.00, 32.00, 61.00, 101.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (2, 6, N'2026-09-01', 60.50, 24.20, 84.80, 100.50, 31.80, 60.40, 100.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (3, 6, N'2026-10-01', 59.00, 23.40, 83.60, 100.00, 31.60, 59.80, 99.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (4, 7, N'2026-08-01', 69.00, 25.00, 87.00, 102.00, 33.00, 62.00, 102.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (5, 7, N'2026-09-01', 67.50, 24.20, 85.80, 101.50, 32.80, 61.40, 101.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (6, 7, N'2026-10-01', 66.00, 23.40, 84.60, 101.00, 32.60, 60.80, 100.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (7, 8, N'2026-08-01', 76.00, 25.00, 88.00, 103.00, 34.00, 55.00, 103.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (8, 8, N'2026-09-01', 74.50, 24.20, 86.80, 102.50, 33.80, 54.40, 102.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (9, 8, N'2026-10-01', 73.00, 23.40, 85.60, 102.00, 33.60, 53.80, 101.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (10, 9, N'2026-08-01', 83.00, 25.00, 89.00, 104.00, 35.00, 56.00, 104.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (11, 9, N'2026-09-01', 81.50, 24.20, 87.80, 103.50, 34.80, 55.40, 103.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (12, 9, N'2026-10-01', 80.00, 23.40, 86.60, 103.00, 34.60, 54.80, 102.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (13, 10, N'2026-08-01', 90.00, 25.00, 90.00, 95.00, 36.00, 57.00, 95.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (14, 10, N'2026-09-01', 88.50, 24.20, 88.80, 94.50, 35.80, 56.40, 94.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (15, 10, N'2026-10-01', 87.00, 23.40, 87.60, 94.00, 35.60, 55.80, 93.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (16, 11, N'2026-08-01', 97.00, 25.00, 91.00, 96.00, 37.00, 58.00, 96.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (17, 11, N'2026-09-01', 95.50, 24.20, 89.80, 95.50, 36.80, 57.40, 95.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (18, 11, N'2026-10-01', 94.00, 23.40, 88.60, 95.00, 36.60, 56.80, 94.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (19, 12, N'2026-08-01', 64.00, 25.00, 80.00, 97.00, 32.00, 59.00, 97.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (20, 12, N'2026-09-01', 62.50, 24.20, 78.80, 96.50, 31.80, 58.40, 96.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (21, 12, N'2026-10-01', 61.00, 23.40, 77.60, 96.00, 31.60, 57.80, 95.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (22, 13, N'2026-08-01', 71.00, 25.00, 81.00, 98.00, 33.00, 60.00, 98.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (23, 13, N'2026-09-01', 69.50, 24.20, 79.80, 97.50, 32.80, 59.40, 97.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (24, 13, N'2026-10-01', 68.00, 23.40, 78.60, 97.00, 32.60, 58.80, 96.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (25, 14, N'2026-08-01', 78.00, 25.00, 82.00, 99.00, 34.00, 61.00, 99.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (26, 14, N'2026-09-01', 76.50, 24.20, 80.80, 98.50, 33.80, 60.40, 98.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (27, 14, N'2026-10-01', 75.00, 23.40, 79.60, 98.00, 33.60, 59.80, 97.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (28, 15, N'2026-08-01', 85.00, 25.00, 83.00, 100.00, 35.00, 62.00, 100.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (29, 15, N'2026-09-01', 83.50, 24.20, 81.80, 99.50, 34.80, 61.40, 99.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (30, 15, N'2026-10-01', 82.00, 23.40, 80.60, 99.00, 34.60, 60.80, 98.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (31, 16, N'2026-08-01', 92.00, 25.00, 84.00, 101.00, 36.00, 55.00, 101.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (32, 16, N'2026-09-01', 90.50, 24.20, 82.80, 100.50, 35.80, 54.40, 100.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (33, 16, N'2026-10-01', 89.00, 23.40, 81.60, 100.00, 35.60, 53.80, 99.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (34, 17, N'2026-08-01', 99.00, 25.00, 85.00, 102.00, 37.00, 56.00, 102.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (35, 17, N'2026-09-01', 97.50, 24.20, 83.80, 101.50, 36.80, 55.40, 101.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (36, 17, N'2026-10-01', 96.00, 23.40, 82.60, 101.00, 36.60, 54.80, 100.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (37, 18, N'2026-08-01', 66.00, 25.00, 86.00, 103.00, 32.00, 57.00, 103.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (38, 18, N'2026-09-01', 64.50, 24.20, 84.80, 102.50, 31.80, 56.40, 102.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (39, 18, N'2026-10-01', 63.00, 23.40, 83.60, 102.00, 31.60, 55.80, 101.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (40, 19, N'2026-08-01', 73.00, 25.00, 87.00, 104.00, 33.00, 58.00, 104.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (41, 19, N'2026-09-01', 71.50, 24.20, 85.80, 103.50, 32.80, 57.40, 103.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (42, 19, N'2026-10-01', 70.00, 23.40, 84.60, 103.00, 32.60, 56.80, 102.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (43, 20, N'2026-08-01', 80.00, 25.00, 88.00, 95.00, 34.00, 59.00, 95.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (44, 20, N'2026-09-01', 78.50, 24.20, 86.80, 94.50, 33.80, 58.40, 94.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (45, 20, N'2026-10-01', 77.00, 23.40, 85.60, 94.00, 33.60, 57.80, 93.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (46, 21, N'2026-08-01', 87.00, 25.00, 89.00, 96.00, 35.00, 60.00, 96.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (47, 21, N'2026-09-01', 85.50, 24.20, 87.80, 95.50, 34.80, 59.40, 95.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (48, 21, N'2026-10-01', 84.00, 23.40, 86.60, 95.00, 34.60, 58.80, 94.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (49, 22, N'2026-08-01', 94.00, 25.00, 90.00, 97.00, 36.00, 61.00, 97.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (50, 22, N'2026-09-01', 92.50, 24.20, 88.80, 96.50, 35.80, 60.40, 96.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (51, 22, N'2026-10-01', 91.00, 23.40, 87.60, 96.00, 35.60, 59.80, 95.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (52, 23, N'2026-08-01', 61.00, 25.00, 91.00, 98.00, 37.00, 62.00, 98.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (53, 23, N'2026-09-01', 59.50, 24.20, 89.80, 97.50, 36.80, 61.40, 97.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (54, 23, N'2026-10-01', 58.00, 23.40, 88.60, 97.00, 36.60, 60.80, 96.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (55, 24, N'2026-08-01', 68.00, 25.00, 80.00, 99.00, 32.00, 55.00, 99.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (56, 24, N'2026-09-01', 66.50, 24.20, 78.80, 98.50, 31.80, 54.40, 98.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (57, 24, N'2026-10-01', 65.00, 23.40, 77.60, 98.00, 31.60, 53.80, 97.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (58, 25, N'2026-08-01', 75.00, 25.00, 81.00, 100.00, 33.00, 56.00, 100.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (59, 25, N'2026-09-01', 73.50, 24.20, 79.80, 99.50, 32.80, 55.40, 99.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (60, 25, N'2026-10-01', 72.00, 23.40, 78.60, 99.00, 32.60, 54.80, 98.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (61, 26, N'2026-08-01', 82.00, 25.00, 82.00, 101.00, 34.00, 57.00, 101.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (62, 26, N'2026-09-01', 80.50, 24.20, 80.80, 100.50, 33.80, 56.40, 100.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (63, 26, N'2026-10-01', 79.00, 23.40, 79.60, 100.00, 33.60, 55.80, 99.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (64, 27, N'2026-08-01', 89.00, 25.00, 83.00, 102.00, 35.00, 58.00, 102.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (65, 27, N'2026-09-01', 87.50, 24.20, 81.80, 101.50, 34.80, 57.40, 101.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (66, 27, N'2026-10-01', 86.00, 23.40, 80.60, 101.00, 34.60, 56.80, 100.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (67, 28, N'2026-08-01', 96.00, 25.00, 84.00, 103.00, 36.00, 59.00, 103.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (68, 28, N'2026-09-01', 94.50, 24.20, 82.80, 102.50, 35.80, 58.40, 102.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (69, 28, N'2026-10-01', 93.00, 23.40, 81.60, 102.00, 35.60, 57.80, 101.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (70, 29, N'2026-08-01', 63.00, 25.00, 85.00, 104.00, 37.00, 60.00, 104.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (71, 29, N'2026-09-01', 61.50, 24.20, 83.80, 103.50, 36.80, 59.40, 103.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (72, 29, N'2026-10-01', 60.00, 23.40, 82.60, 103.00, 36.60, 58.80, 102.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (73, 30, N'2026-08-01', 70.00, 25.00, 86.00, 95.00, 32.00, 61.00, 95.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (74, 30, N'2026-09-01', 68.50, 24.20, 84.80, 94.50, 31.80, 60.40, 94.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (75, 30, N'2026-10-01', 67.00, 23.40, 83.60, 94.00, 31.60, 59.80, 93.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (76, 31, N'2026-08-01', 77.00, 25.00, 87.00, 96.00, 33.00, 62.00, 96.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (77, 31, N'2026-09-01', 75.50, 24.20, 85.80, 95.50, 32.80, 61.40, 95.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (78, 31, N'2026-10-01', 74.00, 23.40, 84.60, 95.00, 32.60, 60.80, 94.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (79, 32, N'2026-08-01', 84.00, 25.00, 88.00, 97.00, 34.00, 55.00, 97.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (80, 32, N'2026-09-01', 82.50, 24.20, 86.80, 96.50, 33.80, 54.40, 96.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (81, 32, N'2026-10-01', 81.00, 23.40, 85.60, 96.00, 33.60, 53.80, 95.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (82, 33, N'2026-08-01', 91.00, 25.00, 89.00, 98.00, 35.00, 56.00, 98.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (83, 33, N'2026-09-01', 89.50, 24.20, 87.80, 97.50, 34.80, 55.40, 97.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (84, 33, N'2026-10-01', 88.00, 23.40, 86.60, 97.00, 34.60, 54.80, 96.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (85, 34, N'2026-08-01', 98.00, 25.00, 90.00, 99.00, 36.00, 57.00, 99.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (86, 34, N'2026-09-01', 96.50, 24.20, 88.80, 98.50, 35.80, 56.40, 98.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (87, 34, N'2026-10-01', 95.00, 23.40, 87.60, 98.00, 35.60, 55.80, 97.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (88, 35, N'2026-08-01', 65.00, 25.00, 91.00, 100.00, 37.00, 58.00, 100.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (89, 35, N'2026-09-01', 63.50, 24.20, 89.80, 99.50, 36.80, 57.40, 99.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (90, 35, N'2026-10-01', 62.00, 23.40, 88.60, 99.00, 36.60, 56.80, 98.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (91, 36, N'2026-08-01', 72.00, 25.00, 80.00, 101.00, 32.00, 59.00, 101.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (92, 36, N'2026-09-01', 70.50, 24.20, 78.80, 100.50, 31.80, 58.40, 100.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (93, 36, N'2026-10-01', 69.00, 23.40, 77.60, 100.00, 31.60, 57.80, 99.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (94, 37, N'2026-08-01', 79.00, 25.00, 81.00, 102.00, 33.00, 60.00, 102.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (95, 37, N'2026-09-01', 77.50, 24.20, 79.80, 101.50, 32.80, 59.40, 101.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (96, 37, N'2026-10-01', 76.00, 23.40, 78.60, 101.00, 32.60, 58.80, 100.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (97, 38, N'2026-08-01', 86.00, 25.00, 82.00, 103.00, 34.00, 61.00, 103.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (98, 38, N'2026-09-01', 84.50, 24.20, 80.80, 102.50, 33.80, 60.40, 102.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (99, 38, N'2026-10-01', 83.00, 23.40, 79.60, 102.00, 33.60, 59.80, 101.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (100, 39, N'2026-08-01', 93.00, 25.00, 83.00, 104.00, 35.00, 62.00, 104.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (101, 39, N'2026-09-01', 91.50, 24.20, 81.80, 103.50, 34.80, 61.40, 103.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (102, 39, N'2026-10-01', 90.00, 23.40, 80.60, 103.00, 34.60, 60.80, 102.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (103, 40, N'2026-08-01', 60.00, 25.00, 84.00, 95.00, 36.00, 55.00, 95.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (104, 40, N'2026-09-01', 58.50, 24.20, 82.80, 94.50, 35.80, 54.40, 94.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (105, 40, N'2026-10-01', 57.00, 23.40, 81.60, 94.00, 35.60, 53.80, 93.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (106, 41, N'2026-08-01', 67.00, 25.00, 85.00, 96.00, 37.00, 56.00, 96.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (107, 41, N'2026-09-01', 65.50, 24.20, 83.80, 95.50, 36.80, 55.40, 95.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (108, 41, N'2026-10-01', 64.00, 23.40, 82.60, 95.00, 36.60, 54.80, 94.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (109, 42, N'2026-08-01', 74.00, 25.00, 86.00, 97.00, 32.00, 57.00, 97.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (110, 42, N'2026-09-01', 72.50, 24.20, 84.80, 96.50, 31.80, 56.40, 96.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (111, 42, N'2026-10-01', 71.00, 23.40, 83.60, 96.00, 31.60, 55.80, 95.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (112, 43, N'2026-08-01', 81.00, 25.00, 87.00, 98.00, 33.00, 58.00, 98.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (113, 43, N'2026-09-01', 79.50, 24.20, 85.80, 97.50, 32.80, 57.40, 97.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (114, 43, N'2026-10-01', 78.00, 23.40, 84.60, 97.00, 32.60, 56.80, 96.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (115, 44, N'2026-08-01', 88.00, 25.00, 88.00, 99.00, 34.00, 59.00, 99.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (116, 44, N'2026-09-01', 86.50, 24.20, 86.80, 98.50, 33.80, 58.40, 98.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (117, 44, N'2026-10-01', 85.00, 23.40, 85.60, 98.00, 33.60, 57.80, 97.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (118, 45, N'2026-08-01', 95.00, 25.00, 89.00, 100.00, 35.00, 60.00, 100.00, N'Medición inicial', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (119, 45, N'2026-09-01', 93.50, 24.20, 87.80, 99.50, 34.80, 59.40, 99.30, N'Progreso mensual', N'2026-10-17T00:49:41.347');
INSERT INTO [dbo].[SeguimientoFisico] ([id_seguimiento], [id_persona], [fecha], [peso], [grasa_corporal], [cintura], [pecho], [brazo], [muslo], [cadera], [notas], [fecha_registro]) VALUES (120, 45, N'2026-10-01', 92.00, 23.40, 86.60, 99.00, 34.60, 58.80, 98.60, N'Control trimestral', N'2026-10-17T00:49:41.347');
SET IDENTITY_INSERT [dbo].[SeguimientoFisico] OFF;
GO

PRINT 'Datos: [dbo].[Sistema] (6 filas)';
SET IDENTITY_INSERT [dbo].[Sistema] ON;
INSERT INTO [dbo].[Sistema] ([id_sistema], [nombre_sistema], [color], [baja], [acceso_libre]) VALUES (1, N'BodyPump', N'#0b8f70', N'N', 0);
INSERT INTO [dbo].[Sistema] ([id_sistema], [nombre_sistema], [color], [baja], [acceso_libre]) VALUES (2, N'PowerJump', N'#ffc107', N'N', 0);
INSERT INTO [dbo].[Sistema] ([id_sistema], [nombre_sistema], [color], [baja], [acceso_libre]) VALUES (3, N'Funcional', N'#28a745', N'N', 0);
INSERT INTO [dbo].[Sistema] ([id_sistema], [nombre_sistema], [color], [baja], [acceso_libre]) VALUES (4, N'Zumba', N'#e91e63', N'N', 0);
INSERT INTO [dbo].[Sistema] ([id_sistema], [nombre_sistema], [color], [baja], [acceso_libre]) VALUES (5, N'Artes Marciales', N'#9c27b0', N'N', 0);
INSERT INTO [dbo].[Sistema] ([id_sistema], [nombre_sistema], [color], [baja], [acceso_libre]) VALUES (6, N'Gimnasio', N'#0b8f70', N'N', 1);
SET IDENTITY_INSERT [dbo].[Sistema] OFF;
GO

PRINT 'Datos: [dbo].[Suscripcion] (59 filas)';
SET IDENTITY_INSERT [dbo].[Suscripcion] ON;
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (1, N'2026-07-01', N'2026-08-01', 3, 1);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (3, N'2026-07-03', N'2026-08-03', 2, 3);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (4, N'2026-07-04', N'2026-08-04', 2, 4);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (5, N'2026-10-20', N'2026-11-20', 1, 5);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (6, N'2026-10-20', N'2026-11-20', 1, 6);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (7, N'2026-07-07', N'2026-08-07', 2, 7);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (8, N'2026-07-08', N'2026-08-08', 2, 8);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (9, N'2026-07-09', N'2026-08-09', 2, 9);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (10, N'2026-07-10', N'2026-08-10', 2, 10);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (11, N'2026-07-11', N'2026-08-11', 2, 11);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (12, N'2026-07-12', N'2026-11-12', 1, 12);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (13, N'2026-07-13', N'2026-11-13', 1, 13);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (14, N'2026-07-14', N'2026-11-14', 1, 14);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (15, N'2026-07-15', N'2026-11-15', 1, 15);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (16, N'2026-07-16', N'2026-11-16', 1, 16);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (17, N'2026-07-17', N'2026-11-17', 1, 17);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (18, N'2026-07-18', N'2026-10-18', 1, 18);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (19, N'2026-07-19', N'2026-10-19', 1, 19);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (20, N'2026-07-20', N'2026-10-20', 1, 20);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (21, N'2026-07-21', N'2026-10-21', 1, 21);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (22, N'2026-07-22', N'2026-10-22', 1, 22);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (23, N'2026-07-23', N'2026-10-23', 1, 23);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (24, N'2026-07-24', N'2026-10-24', 1, 24);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (25, N'2026-07-25', N'2026-10-25', 1, 25);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (26, N'2026-07-26', N'2026-10-26', 1, 26);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (27, N'2026-07-27', N'2026-10-27', 1, 27);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (28, N'2026-07-28', N'2026-10-28', 1, 28);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (29, N'2026-07-29', N'2026-10-29', 1, 29);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (30, N'2026-07-30', N'2026-10-30', 1, 30);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (31, N'2026-07-31', N'2026-10-31', 1, 31);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (32, N'2026-08-01', N'2026-11-01', 1, 32);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (33, N'2026-08-02', N'2026-11-02', 1, 33);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (34, N'2026-08-03', N'2026-11-03', 1, 34);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (35, N'2026-08-04', N'2026-11-04', 1, 35);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (36, N'2026-08-05', N'2026-11-05', 1, 36);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (37, N'2026-08-06', N'2026-11-06', 1, 37);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (38, N'2026-08-07', N'2026-11-07', 1, 38);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (39, N'2026-08-08', N'2026-11-08', 1, 39);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (40, N'2026-08-09', N'2026-11-09', 1, 40);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (41, N'2026-10-21', N'2026-11-21', 1, 41);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (42, N'2026-08-11', N'2026-11-11', 1, 42);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (43, N'2026-08-12', N'2026-11-12', 1, 43);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (44, N'2026-08-13', N'2026-11-13', 1, 44);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (45, N'2026-08-14', N'2026-11-14', 1, 45);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (46, N'2026-08-15', N'2026-11-15', 1, 46);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (47, N'2026-08-16', N'2026-11-16', 1, 47);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (48, N'2026-08-17', N'2026-11-17', 1, 48);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (49, N'2026-08-18', N'2026-10-18', 1, 49);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (50, N'2026-08-19', N'2026-10-19', 1, 50);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (51, N'2026-08-20', N'2026-10-20', 1, 51);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (53, N'2026-08-22', N'2026-10-22', 1, 53);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (54, N'2026-08-23', N'2026-10-23', 1, 54);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (55, N'2026-08-24', N'2026-10-24', 1, 55);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (56, N'2026-08-25', N'2026-10-25', 1, 56);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (57, N'2026-08-26', N'2026-10-26', 1, 57);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (58, N'2026-08-27', N'2026-10-27', 1, 58);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (59, N'2026-08-28', N'2026-10-28', 1, 59);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (60, N'2026-08-29', N'2026-10-29', 1, 60);
INSERT INTO [dbo].[Suscripcion] ([id_suscripcion], [fecha_inicio], [fecha_vencimiento], [id_estado], [id_inscripcion]) VALUES (61, N'2026-10-21', N'2026-11-21', 1, 61);
SET IDENTITY_INSERT [dbo].[Suscripcion] OFF;
GO

PRINT 'Datos: [dbo].[Usuario] (12 filas)';
SET IDENTITY_INSERT [dbo].[Usuario] ON;
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (1, N'admin', N'1234', 1);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (2, N'prof_carlos', N'1234', 2);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (3, N'prof_mariana', N'1234', 3);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (4, N'prof_federico', N'1234', 4);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (5, N'prof_camila', N'1234', 5);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (6, N'juanp', N'1234', 6);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (7, N'marial', N'1234', 7);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (8, N'anaf', N'1234', 8);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (9, N'diegor', N'1234', 9);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (10, N'sofiac', N'1234', 10);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (11, N'lucasc', N'1234', 15);
INSERT INTO [dbo].[Usuario] ([id_usuario], [nombre_usuario], [contraseña], [id_persona]) VALUES (12, N'milagrosc', N'1234', 34);
SET IDENTITY_INSERT [dbo].[Usuario] OFF;
GO

PRINT 'Agregando claves foraneas';
GO

ALTER TABLE [dbo].[Asistencia] WITH CHECK ADD CONSTRAINT [FK__Asistenci__id_pe__74AE54BC] FOREIGN KEY ([id_persona]) REFERENCES [dbo].[Persona] ([id_persona]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Horario] WITH CHECK ADD CONSTRAINT [FK__Horario__id_sist__440B1D61] FOREIGN KEY ([id_sistema]) REFERENCES [dbo].[Sistema] ([id_sistema]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Inscripcion] WITH CHECK ADD CONSTRAINT [FK__Inscripci__id_pe__52593CB8] FOREIGN KEY ([id_persona]) REFERENCES [dbo].[Persona] ([id_persona]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Inscripcion] WITH CHECK ADD CONSTRAINT [FK__Inscripci__id_si__534D60F1] FOREIGN KEY ([id_sistema]) REFERENCES [dbo].[Sistema] ([id_sistema]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Inscripcion] WITH CHECK ADD CONSTRAINT [FK_Inscripcion_Horario] FOREIGN KEY ([id_horario]) REFERENCES [dbo].[Horario] ([id_horario]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Mensualidad] WITH CHECK ADD CONSTRAINT [FK__Mensualid__id_si__46E78A0C] FOREIGN KEY ([id_sistema]) REFERENCES [dbo].[Sistema] ([id_sistema]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Pago] WITH CHECK ADD CONSTRAINT [FK__Pago__cobrado_po__5BE2A6F2] FOREIGN KEY ([cobrado_por]) REFERENCES [dbo].[Usuario] ([id_usuario]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Pago] WITH CHECK ADD CONSTRAINT [FK__Pago__id_medio_p__59FA5E80] FOREIGN KEY ([id_medio_pago]) REFERENCES [dbo].[Medio_pago] ([id_medio_pago]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Pago] WITH CHECK ADD CONSTRAINT [FK__Pago__id_mensual__5AEE82B9] FOREIGN KEY ([id_mensualidad]) REFERENCES [dbo].[Mensualidad] ([id_mensualidad]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Pago] WITH CHECK ADD CONSTRAINT [FK__Pago__id_suscrip__5CD6CB2B] FOREIGN KEY ([id_suscripcion]) REFERENCES [dbo].[Suscripcion] ([id_suscripcion]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Persona] WITH CHECK ADD CONSTRAINT [FK__Persona__id_rol__3B75D760] FOREIGN KEY ([id_rol]) REFERENCES [dbo].[Rol] ([id_rol]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Preferencia_usuario] WITH CHECK ADD CONSTRAINT [FK_Preferencia_usuario_Usuario] FOREIGN KEY ([id_usuario]) REFERENCES [dbo].[Usuario] ([id_usuario]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Reserva] WITH CHECK ADD CONSTRAINT [FK__Reserva__id_hora__6FE99F9F] FOREIGN KEY ([id_horario]) REFERENCES [dbo].[Horario] ([id_horario]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Reserva] WITH CHECK ADD CONSTRAINT [FK__Reserva__id_pers__70DDC3D8] FOREIGN KEY ([id_persona]) REFERENCES [dbo].[Persona] ([id_persona]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Rutina] WITH CHECK ADD CONSTRAINT [FK__Rutina__id_clien__6477ECF3] FOREIGN KEY ([id_cliente]) REFERENCES [dbo].[Persona] ([id_persona]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Rutina] WITH CHECK ADD CONSTRAINT [FK__Rutina__id_profe__6383C8BA] FOREIGN KEY ([id_profesor]) REFERENCES [dbo].[Persona] ([id_persona]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[RutinaDia] WITH CHECK ADD CONSTRAINT [FK__RutinaDia__id_ru__6B24EA82] FOREIGN KEY ([id_rutina]) REFERENCES [dbo].[Rutina] ([id_rutina]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[RutinaEjercicio] WITH CHECK ADD CONSTRAINT [FK__RutinaEje__id_ru__68487DD7] FOREIGN KEY ([id_rutina]) REFERENCES [dbo].[Rutina] ([id_rutina]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[SeguimientoFisico] WITH CHECK ADD CONSTRAINT [FK__Seguimien__id_pe__787EE5A0] FOREIGN KEY ([id_persona]) REFERENCES [dbo].[Persona] ([id_persona]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Suscripcion] WITH CHECK ADD CONSTRAINT [FK__Suscripci__id_es__5629CD9C] FOREIGN KEY ([id_estado]) REFERENCES [dbo].[Estado_suscripcion] ([id_estado]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Suscripcion] WITH CHECK ADD CONSTRAINT [FK__Suscripci__id_in__571DF1D5] FOREIGN KEY ([id_inscripcion]) REFERENCES [dbo].[Inscripcion] ([id_inscripcion]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO
ALTER TABLE [dbo].[Usuario] WITH CHECK ADD CONSTRAINT [FK__Usuario__id_pers__4AB81AF0] FOREIGN KEY ([id_persona]) REFERENCES [dbo].[Persona] ([id_persona]) ON UPDATE NO ACTION ON DELETE NO ACTION;
GO

PRINT 'Script finalizado.';
GO
