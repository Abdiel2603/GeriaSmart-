-- Crear BD si no existe y seleccionarla
CREATE DATABASE IF NOT EXISTS geriasmart_db;
USE geriasmart_db;

-- Tablas
CREATE TABLE IF NOT EXISTS usuario(
    id_usr INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nom_usr VARCHAR(255) NOT NULL,
    mail VARCHAR(255) NOT NULL,
    pass VARCHAR(255) NOT NULL,
    tel_usr CHAR(10) NOT NULL,
    tip_usu INT NOT NULL DEFAULT 3  -- 1 = administrador, 3 = cuidador (por defecto)
    );

-- Datos de ejemplo actualizados
INSERT INTO usuario (id_usr, nom_usr, mail, pass, tel_usr, tip_usu) VALUES
(1, 'Iván Taid Ruiz Alcaraz', 'ivan@gmail.com', 'ivan123', '4427216981', 1),  -- admin
(2, 'José Abdiel Bastida Mata', 'abdiel@gmail.com', 'abdiel123', '4421234567', 3);  -- cuidador

DROP TABLE IF EXISTS paciente;
CREATE TABLE IF NOT EXISTS paciente (
  id_paciente INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nom_paciente VARCHAR(255) NOT NULL,
  mail         VARCHAR(255) NOT NULL UNIQUE,
  pass         VARCHAR(255) NOT NULL,
  fecha_nacimiento DATE NOT NULL,               -- formato: YYYY-MM-DD
  genero ENUM('masculino','femenino') NOT NULL,
  peso INT NOT NULL,                   -- se mostrará en kg
  estatura INT NOT NULL,               -- se mostrará en cm
  padecimientos TEXT NULL                       -- opcional
);

-- Datos de ejemplo (incluyendo Alan con id 100)
INSERT INTO paciente
(id_paciente, nom_paciente, mail, pass, fecha_nacimiento, genero, peso, estatura, padecimientos) VALUES
(1,   'Juan Pérez López', 'juan@gmail.com', 'juan123', '1954-05-10', 'masculino', 70, 170, 'Hipertensión arterial'),
(2,   'María García Ruiz', 'maria@gmail.com', 'maria123', '1944-09-22', 'femenino',  62, 158, 'Diabetes tipo 2'),
(100, 'Alan Kevin', 'alan@gmail.com','alan123', '2000-01-01', 'masculino', 80, 175, NULL);

DROP TABLE IF EXISTS cuidador_paciente;
CREATE TABLE IF NOT EXISTS cuidador_paciente (
  id_cuidador INT(5) NOT NULL,
  id_paciente INT(5) NOT NULL,
  fecha_vinculacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cuidador, id_paciente),
  FOREIGN KEY (id_cuidador) REFERENCES usuario(id_usr)   ON DELETE CASCADE,
  FOREIGN KEY (id_paciente) REFERENCES paciente(id_paciente) ON DELETE CASCADE
);

-- Datos de ejemplo de vinculaciones
INSERT INTO cuidador_paciente (id_cuidador, id_paciente) VALUES
(2, 1),    -- Abdiel cuida a Juan
(2, 2);    -- Abdiel cuida a María

DROP TABLE IF EXISTS signos_vitales;
CREATE TABLE IF NOT EXISTS signos_vitales (
  id_signo INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_paciente INT(5) NOT NULL,
  tipo_signo ENUM('nivel_azucar','temperatura','peso','ritmo_cardiaco','presion_arterial') NOT NULL,
  valor DECIMAL(6,2) NOT NULL,
  fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_paciente) REFERENCES paciente(id_paciente) ON DELETE CASCADE
);

-- Datos de ejemplo (pensando en una semana reciente)
INSERT INTO signos_vitales (id_paciente, tipo_signo, valor, fecha_registro) VALUES
(1, 'presion_arterial', 130.00, '2026-03-01 08:00:00'),
(1, 'ritmo_cardiaco',    72.00, '2026-03-01 08:00:00'),
(1, 'nivel_azucar',     110.00, '2026-03-01 08:05:00'),
(1, 'temperatura',       36.80, '2026-03-02 08:00:00'),
(1, 'peso',              70.50, '2026-03-02 08:00:00'),
(2, 'presion_arterial', 140.00, '2026-03-01 09:00:00'),
(2, 'ritmo_cardiaco',    78.00, '2026-03-01 09:00:00'),
(2, 'nivel_azucar',     145.00, '2026-03-02 09:10:00'),
(100,'peso',             80.00, '2026-03-01 07:30:00'),
(100,'temperatura',      36.70, '2026-03-01 07:30:00');

-- Más signos vitales para simular una semana
-- Paciente 1: Juan Pérez López
INSERT INTO signos_vitales (id_paciente, tipo_signo, valor, fecha_registro) VALUES
(1, 'presion_arterial', 128.00, '2026-03-02 08:00:00'),
(1, 'nivel_azucar',     112.00, '2026-03-02 08:05:00'),

(1, 'presion_arterial', 132.00, '2026-03-03 08:00:00'),
(1, 'nivel_azucar',     115.00, '2026-03-03 08:05:00'),

(1, 'presion_arterial', 129.00, '2026-03-04 08:00:00'),
(1, 'nivel_azucar',     118.00, '2026-03-04 08:05:00'),

(1, 'presion_arterial', 131.00, '2026-03-05 08:00:00'),
(1, 'nivel_azucar',     116.00, '2026-03-05 08:05:00'),

(1, 'presion_arterial', 127.00, '2026-03-06 08:00:00'),
(1, 'nivel_azucar',     113.00, '2026-03-06 08:05:00'),

(1, 'presion_arterial', 130.00, '2026-03-07 08:00:00'),
(1, 'nivel_azucar',     111.00, '2026-03-07 08:05:00');

-- Paciente 2: María García Ruiz
INSERT INTO signos_vitales (id_paciente, tipo_signo, valor, fecha_registro) VALUES
(2, 'presion_arterial', 142.00, '2026-03-02 09:00:00'),
(2, 'nivel_azucar',     150.00, '2026-03-02 09:10:00'),

(2, 'presion_arterial', 139.00, '2026-03-03 09:00:00'),
(2, 'nivel_azucar',     148.00, '2026-03-03 09:10:00'),

(2, 'presion_arterial', 141.00, '2026-03-04 09:00:00'),
(2, 'nivel_azucar',     152.00, '2026-03-04 09:10:00'),

(2, 'presion_arterial', 138.00, '2026-03-05 09:00:00'),
(2, 'nivel_azucar',     147.00, '2026-03-05 09:10:00'),

(2, 'presion_arterial', 140.00, '2026-03-06 09:00:00'),
(2, 'nivel_azucar',     149.00, '2026-03-06 09:10:00'),

(2, 'presion_arterial', 137.00, '2026-03-07 09:00:00'),
(2, 'nivel_azucar',     145.00, '2026-03-07 09:10:00');

-- Paciente 100: Alan Kevin
INSERT INTO signos_vitales (id_paciente, tipo_signo, valor, fecha_registro) VALUES
(100, 'peso',        80.00, '2026-03-01 07:30:00'),
(100, 'temperatura', 36.70, '2026-03-01 07:30:00'),

(100, 'peso',        79.90, '2026-03-02 07:30:00'),
(100, 'temperatura', 36.80, '2026-03-02 07:30:00'),

(100, 'peso',        79.85, '2026-03-03 07:30:00'),
(100, 'temperatura', 36.70, '2026-03-03 07:30:00'),

(100, 'peso',        79.80, '2026-03-04 07:30:00'),
(100, 'temperatura', 36.75, '2026-03-04 07:30:00'),

(100, 'peso',        79.75, '2026-03-05 07:30:00'),
(100, 'temperatura', 36.70, '2026-03-05 07:30:00'),

(100, 'peso',        79.70, '2026-03-06 07:30:00'),
(100, 'temperatura', 36.80, '2026-03-06 07:30:00'),

(100, 'peso',        79.65, '2026-03-07 07:30:00'),
(100, 'temperatura', 36.75, '2026-03-07 07:30:00');

DROP TABLE IF EXISTS cita;
CREATE TABLE IF NOT EXISTS cita (
  id_cita INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_paciente INT(5) NOT NULL,
  titulo VARCHAR(255) NOT NULL,
  fecha DATE NOT NULL,
  hora TIME NOT NULL,
  descripcion TEXT,
  FOREIGN KEY (id_paciente) REFERENCES paciente(id_paciente) ON DELETE CASCADE
);

-- Datos de ejemplo
INSERT INTO cita (id_paciente, titulo, fecha, hora, descripcion) VALUES
(1, 'Consulta de control de hipertensión', '2026-03-10', '09:30:00', 'Revisión de presión arterial y ajuste de medicamento'),
(1, 'Análisis de laboratorio',            '2026-03-12', '08:00:00', 'Entrega de muestras de sangre'),
(2, 'Consulta de control de diabetes',    '2026-03-11', '10:15:00', 'Revisión de niveles de glucosa y dieta');

-- 5 citas por paciente

INSERT INTO cita (id_paciente, titulo, fecha, hora, descripcion) VALUES
-- Paciente 1: Juan Pérez López
(1, 'Consulta de control de hipertensión', '2026-03-10', '09:30:00', 'Revisión de presión arterial y ajuste de medicamento'),
(1, 'Análisis de laboratorio',            '2026-03-12', '08:00:00', 'Entrega de muestras de sangre'),
(1, 'Electrocardiograma',                 '2026-03-15', '11:00:00', 'Evaluación del estado cardiaco'),
(1, 'Consulta de seguimiento general',    '2026-03-20', '10:00:00', 'Revisión de resultados de laboratorio'),
(1, 'Revisión de medicamentos',           '2026-03-25', '09:15:00', 'Evaluar efectos secundarios y dosis'),

-- Paciente 2: María García Ruiz
(2, 'Consulta de control de diabetes',    '2026-03-11', '10:15:00', 'Revisión de niveles de glucosa y dieta'),
(2, 'Análisis de glucosa en ayunas',      '2026-03-13', '07:30:00', 'Toma de muestra en laboratorio'),
(2, 'Consulta nutricional',               '2026-03-18', '12:00:00', 'Ajuste de plan alimenticio'),
(2, 'Consulta de pie diabético',          '2026-03-22', '09:45:00', 'Revisión de extremidades inferiores'),
(2, 'Consulta de seguimiento general',    '2026-03-28', '11:30:00', 'Evaluación global de control de diabetes'),

-- Paciente 100: Alan Kevin
(100, 'Consulta de valoración general',   '2026-03-09', '08:30:00', 'Chequeo general y control de peso'),
(100, 'Consulta de nutrición',            '2026-03-14', '10:00:00', 'Revisión de hábitos alimenticios'),
(100, 'Análisis de laboratorio anual',    '2026-03-19', '07:45:00', 'Perfil general de laboratorio'),
(100, 'Consulta de seguimiento',          '2026-03-23', '09:00:00', 'Revisión de resultados y ajustes'),
(100, 'Revisión de estilo de vida',       '2026-03-30', '11:15:00', 'Recomendaciones de actividad física y dieta');

DROP TABLE IF EXISTS medicamento;
CREATE TABLE IF NOT EXISTS medicamento (
  id_medicamento INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  id_paciente INT(5) NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  dosis VARCHAR(50) NOT NULL,              -- Ej: "1 tableta", "10 ml"
  cantidad_total INT NOT NULL,             -- Total inicial (opcional para control de stock)
  cantidad_restante INT NOT NULL,          -- Se decrementa con cada toma registrada
  frecuencia_horas INT NOT NULL,           -- Cada cuántas horas
  dias_tratamiento INT NOT NULL DEFAULT 1, -- 1 = dosis única, >1 = tratamiento prolongado
  nota TEXT NULL,
  FOREIGN KEY (id_paciente) REFERENCES paciente(id_paciente) ON DELETE CASCADE
);

-- Datos de ejemplo
INSERT INTO medicamento (id_paciente, nombre, dosis, cantidad_total, cantidad_restante, frecuencia_horas, dias_tratamiento, nota) VALUES
(1, 'Losartán 50 mg',   '1 tableta', 30, 28, 24, 30, 'Tomar por la mañana, después del desayuno'),
(1, 'Metformina 850 mg','1 tableta', 60, 55, 12, 30, 'Tomar con alimentos para evitar malestar estomacal'),
(2, 'Paracetamol 500 mg','1 tableta',10,  9,  8,  3, 'Solo en caso de dolor o fiebre');

-- 5 medicamentos por paciente
-- Algunos ya finalizados (cantidad_restante = 0) y otros en curso (>0)

INSERT INTO medicamento
(id_paciente, nombre, dosis, cantidad_total, cantidad_restante, frecuencia_horas, dias_tratamiento, nota) VALUES

-- Paciente 1: Juan Pérez López
(1, 'Losartán 50 mg',       '1 tableta', 30,  0, 24, 30, 'Tratamiento ya concluido, tomar en caso de nueva indicación médica'),
(1, 'Metformina 850 mg',    '1 tableta', 60, 40, 12, 30, 'Tomar con alimentos para evitar malestar estomacal'),
(1, 'Aspirina 100 mg',      '1 tableta', 20,  0, 24, 20, 'Uso como antiagregante plaquetario, tratamiento finalizado'),
(1, 'Atorvastatina 20 mg',  '1 tableta', 30, 25, 24, 30, 'Tomar por la noche para control de colesterol'),
(1, 'Omeprazol 20 mg',      '1 cápsula',14,  5, 24, 14, 'Protección gástrica durante el tratamiento actual'),

-- Paciente 2: María García Ruiz
(2, 'Metformina 850 mg',    '1 tableta', 60,  0, 12, 60, 'Tratamiento previo concluido, pendiente nueva indicación'),
(2, 'Insulina NPH',         '10 unidades', 30, 12, 12, 30, 'Aplicar según indicación médica, conservar en refrigeración'),
(2, 'Losartán 50 mg',       '1 tableta', 30, 18, 24, 30, 'Control de presión arterial'),
(2, 'Paracetamol 500 mg',   '1 tableta', 10,  3,  8,  3, 'Solo en caso de dolor o fiebre'),
(2, 'Complejo B',           '1 tableta', 20,  0, 24, 20, 'Suplemento vitamínico, tratamiento finalizado'),

-- Paciente 100: Alan Kevin
(100, 'Multivitamínico',    '1 tableta', 30, 27, 24, 30, 'Tomar por la mañana con el desayuno'),
(100, 'Ibuprofeno 400 mg',  '1 tableta', 15,  0,  8,  5, 'Tratamiento corto para dolor muscular, ya concluido'),
(100, 'Omeprazol 20 mg',    '1 cápsula',14,  2, 24, 14, 'Tomar en ayunas si hay molestia gástrica'),
(100, 'Vitamina D 2000 UI', '1 cápsula',30, 20, 48, 60, 'Suplemento para salud ósea, en curso'),
(100, 'Loratadina 10 mg',   '1 tableta',10,  6, 24, 10, 'Tomar solo en caso de alergia o rinitis');


CREATE TABLE IF NOT EXISTS comentarios(
                                          id_com INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    comentario VARCHAR(255) NOT NULL,
    fecha DATETIME NOT NULL,
    estatus INT NOT NULL,
    usr_id INT NOT NULL,
    blog_id INT NOT NULL
    );

CREATE TABLE IF NOT EXISTS blog(
                                   id_blog INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    `desc` TEXT NOT NULL,
    img VARCHAR(255) NOT NULL,
    tags VARCHAR(255) NOT NULL
    );

CREATE TABLE IF NOT EXISTS catalogo(
                                       id_prod INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nom_prod VARCHAR(255) NOT NULL,
    `desc` VARCHAR(500) NOT NULL,
    prec DECIMAL(8, 2) NOT NULL,
    modelo VARCHAR(20) NOT NULL,
    img VARCHAR(500) NOT NULL,
    estatus INT NOT NULL,
    stock INT NOT NULL
    );

CREATE TABLE IF NOT EXISTS compras (
                                       id_compra   INT(5)      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_usr      INT(5)      NOT NULL,
    id_prod     INT(5)      NOT NULL,
    total       DECIMAL(10,2) NOT NULL,  -- Monto cobrado en la compra
    fecha_compra DATETIME   DEFAULT CURRENT_TIMESTAMP,
    estado      INT         NOT NULL DEFAULT 1 COMMENT '1 = Completada, 0 = Cancelada',
    num_tarjeta VARCHAR(20) NOT NULL COMMENT 'Últimos 4 dígitos simulados',
    FOREIGN KEY (id_usr)  REFERENCES usuario(id_usr)   ON DELETE CASCADE,
    FOREIGN KEY (id_prod) REFERENCES catalogo(id_prod) ON DELETE CASCADE
    );

-- Producto de prueba
INSERT INTO catalogo (nom_prod, `desc`, prec, modelo, img, estatus, stock)
VALUES ('GeriaBand', 'GeriaBand modelo básico con características base y las funciones mas esenciales para el cuidado eficiente, una excelente opción para empezar a utilizar el ecosistema GeriaSmart.', 300.00, 'Basic', 'https://i.imgur.com/4xhCLHY.png', 1, 200);
