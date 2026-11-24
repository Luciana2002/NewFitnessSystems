/* ============================================================================
   NewFitnessSystems - SOLO ESQUEMA (sin INSERT)
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