# 🏛️ Sistema de Gestión y Control Formativo — INCES Industrial

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-Analytics-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-success?style=for-the-badge)

**Plataforma integral web para la administración, seguimiento pedagógico, auditoría, control de metas formativas y emisión de reportes estadísticos del INCES (Instituto Nacional de Capacitación y Educación Socialista).**

[Características](#-características-principales) •
[Estructura](#-arquitectura-del-proyecto) •
[Requisitos](#-requisitos-del-sistema) •
[Instalación](#-instalación-y-configuración) •
[Módulos](#-módulos-del-sistema) •
[Seguridad](#-seguridad-y-roles)

</div>

---

## 📖 Descripción General

El **Sistema de Gestión INCES** es una solución web empresarial desarrollada bajo el patrón de arquitectura **MVC (Modelo-Vista-Controlador)** que centraliza y optimiza los flujos de trabajo administrativos y académicos en los Centros de Formación Socialista (CFS).

Permite llevar un seguimiento pormenorizado del ciclo formativo de los participantes, asignación de cursos, gestión de docentes y facilitadores, auditoría de actividades en tiempo real y supervisión de metas operativas a través de tableros con semaforización analítica.

---

## ✨ Características Principales

- 📊 **Dashboard Ejecutivo & KPIs**: Indicadores en tiempo real sobre participantes inscritos, cursos vigentes, docentes activos y porcentaje de avance sobre metas trimestrales y anuales.
- 🚦 **Semaforización de Metas**: Clasificación dinámica del cumplimiento formativo en estados: *Óptimo* (verde), *Atención* (amarillo) y *Crítico* (rojo).
- 👥 **Gestión Integral de Participantes**: Ficha integral con datos demográficos, condición de discapacidad, nivel educativo, validación de cédulas en tiempo real y asignación a programas formativos.
- 📚 **Control de Cursos y Áreas Formativas**: Administración de unidades curriculares, modalidades, horas académicas completadas, control de aforo (cupo mín/máx) y trazabilidad de estados.
- 👨‍🏫 **Gestión de Facilitadores / Docentes**: Control de perfiles pedagógicos, áreas de especialización, asignación curricular y cómputo de carga horaria.
- 📈 **Módulo de Reportes y Analítica Avanzada**: Gráficos interactivos de evolución mensual, distribución por género y ocupación de aulas respaldados con exportación a PDF (**FPDF** y **Dompdf**).
- 🛡️ **Bitácora de Auditoría (Historial)**: Trazabilidad total de operaciones críticas realizadas en la plataforma (registros, actualizaciones, eliminaciones y sesiones).
- 🔒 **Seguridad y Control de Accesos**: Autenticación protegida con hash seguro (`password_hash`), control de inactividad de sesión, cookies seguras y control de accesos basado en roles (RBAC).

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **Lenguaje**: [PHP 8.x](https://www.php.net/) (Programación Orientada a Objetos y PDO).
- **Base de Datos**: [MySQL / MariaDB](https://www.mysql.com/) con motor InnoDB y soporte completo UTF-8 (`utf8mb4`).
- **Librerías PDF**: FPDF & Dompdf para renderizado y generación dinámica de informes institucionales.

### Frontend
- **Estructura y Estilos**: HTML5 Semántico, CSS3 Vanilla personalizado con efectos Glassmorphism, animaciones fluidas y [Bootstrap 5](https://getbootstrap.com/).
- **Interactividad**: JavaScript moderno (ES6+), [jQuery 3.7.1](https://jquery.com/).
- **Visualización de Datos**: [Chart.js](https://www.chartjs.org/) para tableros y gráficas interactivas.
- **Tipografías e Iconografía**: [FontAwesome 6](https://fontawesome.com/) y fuentes del sistema optimizadas.
- **Notificaciones**: Toastr Notifications para alertas reactivas de usuario.

---

## 📂 Arquitectura del Proyecto

El código está estructurado siguiendo una separación clara de responsabilidades:

```plaintext
Sistema de Gestion Inces/
├── app/
│   ├── apis/                   # Endpoints internos y servicios AJAX
│   │   ├── execute-query.php
│   │   └── get-meta-participants.php
│   ├── controlador/            # Lógica de negocio y controladores del sistema
│   │   ├── Curso/              # Controladores para unidades curriculares
│   │   ├── Dashboard/          # Procesamiento de métricas y KPIs
│   │   ├── Docente/            # Lógica para facilitadores
│   │   ├── Historial/          # Trazabilidad y registros de auditoría
│   │   ├── Metas/              # Lógica de metas y semaforización
│   │   ├── ParticipanteCurso/  # Asignación y trayectoria de participantes
│   │   ├── Reporte/            # APIs de reportes y generadores PDF
│   │   ├── Usuario/            # Gestión de cuentas y credenciales
│   │   ├── dompdf/             # Motor de generación PDF
│   │   ├── fpdf/               # Generador de documentos PDF
│   │   └── managerParticipante.php
│   ├── modales/                # Ventanas emergentes para altas, bajas y ediciones
│   ├── modelo/                 # Conexión a datos y lógica relacional
│   │   ├── conexion.php        # Conexión PDO centralizada
│   │   ├── HistorialUtil.php   # Helper de auditoría
│   │   └── IndicadorHelper.php # Helper para cálculo de indicadores y semáforos
│   └── vista/                  # Vistas y plantillas de usuario
│       ├── Dashboard/          # Vistas parciales de métricas
│       ├── partials/           # Menús, barras de navegación y pies de página
│       ├── reportes/           # Plantillas para exportación PDF
│       ├── index.php           # Pantalla de inicio de sesión
│       ├── Inicio.php          # Dashboard principal
│       ├── participante.php    # Módulo de participantes
│       ├── cursos.php          # Módulo de cursos
│       ├── docente.php         # Módulo de docentes
│       ├── metas.php           # Módulo de metas formativas
│       ├── reporte.php         # Módulo de reportes y gráficas
│       ├── Historial.php       # Visor de bitácora del sistema
│       └── usuario.php         # Gestión de usuarios del sistema
├── assets/                     # Recursos estáticos
│   ├── css/                    # Hojas de estilo personalizadas y Bootstrap
│   ├── fontawesome/            # Paquete de iconos
│   ├── img/                    # Logotipos, avatares y recursos gráficos
│   └── js/                     # Scripts de validación, charts y AJAX
├── inces1.sql                  # Script de creación y datos de la base de datos
└── README.md                   # Documentación técnica
```

---

## 📋 Requisitos del Sistema

Para desplegar y ejecutar este proyecto localmente o en servidor se requiere:

- **Servidor Web**: Apache 2.4 o superior (recomendado: [XAMPP](https://www.apachefriends.org/), WampServer o LAMP).
- **PHP**: Versión 8.0, 8.1 o 8.2 con las siguientes extensiones habilitadas:
  - `pdo_mysql`
  - `mbstring`
  - `gd`
  - `curl`
- **Gestor de Base de Datos**: MySQL 5.7+ o MariaDB 10.4+.
- **Navegador Web Moderno**: Google Chrome, Mozilla Firefox, Microsoft Edge, Safari.

---

## 🚀 Instalación y Configuración

Sigue estos sencillos pasos para tener el sistema en funcionamiento en tu entorno local:

### 1. Clonar o descargar el repositorio
Ubica el proyecto dentro del directorio raíz de tu servidor web (por ejemplo en XAMPP: `htdocs`):

```bash
cd c:/xampp/htdocs/
git clone https://github.com/tu-usuario/sistema-de-gestion-inces.git "Sistema de Gestion Inces"
```

### 2. Configurar la Base de Datos
1. Inicia los servicios de **Apache** y **MySQL** desde el panel de control de XAMPP.
2. Ingresa a phpMyAdmin desde tu navegador: `http://localhost/phpmyadmin/`.
3. Crea una nueva base de datos llamada:
   ```sql
   CREATE DATABASE inces1 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```
4. Selecciona la base de datos `inces1` y ve a la pestaña **Importar**.
5. Selecciona el archivo [`inces1.sql`](file:///c:/xampp/htdocs/Sistema%20de%20Gestion%20Inces/inces1.sql) ubicado en la raíz del proyecto y haz clic en **Importar**.

### 3. Configuración de Credenciales
Si tus credenciales locales de MySQL son distintas a las predeterminadas de XAMPP (`root` sin contraseña), modifica el archivo de conexión en [`app/modelo/conexion.php`](file:///c:/xampp/htdocs/Sistema%20de%20Gestion%20Inces/app/modelo/conexion.php):

```php
class Conexion {
    private $host     = "localhost";
    private $dbname   = "inces1";
    private $username = "root";       // Tu usuario de BD
    private $password = "";           // Tu contraseña de BD
    // ...
}
```

### 4. Ejecutar la Aplicación
Abre tu navegador e ingresa a la siguiente dirección:

```text
http://localhost/Sistema%20de%20Gestion%20Inces/app/vista/index.php
```

---

## 👥 Módulos del Sistema

| Módulo | Descripción |
| :--- | :--- |
| **Autenticación & Seguridad** | Inicio de sesión con prevención de ataques por fuerza bruta, cierre por inactividad y recuperación de contraseñas. |
| **Dashboard y Métricas** | Tablero centralizado con KPIs, conteo rápido de matrículas, cursos y docentes, semáforo de metas y gráficos analíticos. |
| **Participantes** | Registro, edición, ficha técnica con discapacidades, asignación a cursos y validación de cédulas por AJAX. |
| **Cursos y Formaciones** | Planificación formativa, áreas de conocimiento, sectores económicos, cupos y horarios. |
| **Facilitadores (Docentes)** | Expediente de docentes, asignación académica y control de horas impartidas. |
| **Control de Metas** | Seguimiento de objetivos por sector y ámbito con indicadores porcentuales de logro. |
| **Reportes & Estadísticas** | Informes detallados y generación de documentos oficiales descargables en formato PDF. |
| **Auditoría (Historial)** | Bitácora que registra usuario, acción, módulo, fecha y hora de cada movimiento en el sistema. |
| **Administración de Usuarios** | Gestión de cuentas con roles diferenciados (*Administrador* y *Apoyo*). |

---

## 🔒 Seguridad y Roles

El sistema implementa control de acceso basado en roles (**RBAC**):

- **Administrador**: Acceso irrestricto a todos los módulos: creación y modificación de usuarios, gestión de metas, auditoría completa, asignaciones y reportería.
- **Apoyo / Operador**: Acceso enfocado a labores operativas: consulta y registro de participantes, gestión de inscripciones y visualización de cursos.

---

## 🤝 Contribución

Las contribuciones son bienvenidas. Si deseas colaborar:

1. Haz un **Fork** del proyecto.
2. Crea una rama para tu función o arreglo:
   ```bash
   git checkout -b feature/NuevaCaracteristica
   ```
3. Realiza tus cambios y haz commit:
   ```bash
   git commit -m "feat: Agregar nueva funcionalidad"
   ```
4. Envía tus cambios a tu repositorio remoto:
   ```bash
   git push origin feature/NuevaCaracteristica
   ```
5. Abre un **Pull Request**.

---

## 📄 Licencia

Este proyecto está bajo la Licencia **MIT**. Consulta el archivo `LICENSE` para más información.

---

<div align="center">
Desarrollado para la optimización de procesos formativos y comunitarios.
</div>
