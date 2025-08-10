# 🦷 Sistema de Gestión para Clínica Odontológica

Este proyecto es una aplicación web diseñada para la gestión integral de pacientes en una clínica odontológica básica (Generada completamente con Inteligencia Artificial usando Google Gemini). La herramienta permite centralizar la información clínica, administrar citas, gestionar diagnósticos y tratamientos, y llevar un control financiero básico.

## ✨ Características Principales

* **Gestión de Pacientes**: Registro, búsqueda, elaboración de planes de tratamientos y procedimientos odontológicos con la gestión de pagos por cada procedimiento.

* **Historial Clínico Digital**: Mantiene un registro detallado de las interacciones, diagnósticos, tratamientos y pagos.

* **Odontograma Interactivo**: Permite registrar el estado dental del paciente y visualizar la evolución de los tratamientos.

* **Agendamiento de Citas**: Calendario para una visualización diaria y mensual de las citas.

* **Gestión de Recetas**: Emisión y almacenamiento de recetas médicas asociadas a cada procedimiento.

* **Reportes y Estadísticas**: Paneles visuales sobre el estado de la clínica, ingresos y deudas.

## 🛠️ Tecnologías Utilizadas

El proyecto fue desarrollado con un stack de tecnologías web de código abierto:

* **Backend**: PHP

* **Base de Datos**: MySQL

* **Frontend**: HTML5, CSS3, JavaScript

* **Framework CSS**: Bootstrap (para un diseño responsive)

* **Visualización de Datos**: Chart.js (para los gráficos de reportes)

## 🚀 Instalación y Requisitos

Para poder ejecutar la aplicación, necesitas tener un entorno de desarrollo web local. Este proyecto fue diseñado para funcionar con **WampServer**, la última versión disponible.

### 📋 Requisitos Previos

* **WampServer (versión más reciente)**: Incluye Apache, MySQL y PHP. Puedes descargarlo de [su sitio oficial](https://www.wampserver.com/en/).

### 💻 Pasos para la Instalación

1.  **Clonar el Repositorio**: Descarga o clona este proyecto en la carpeta `www` de tu instalación de WampServer. Por ejemplo: `C:\wamp64\www\nombre-del-proyecto`.

2.  **Crear la Base de Datos**:

    * Abre la interfaz de WampServer y accede a **phpMyAdmin**.

    * Crea una nueva base de datos llamada `clinica_odontologica`. Descarga la base de datos [aqui](BD/)

    * Restaura la BD `clinica_odontologica.sql` que se encuentra en el proyecto en la carpeta **BD** en phpMyAdmin. Esto creará todas las tablas y los datos iniciales.

3.  **Configurar la Aplicación**:

    * Abre el archivo `configuracion.json` y ajusta los parámetros de la base de datos si es necesario. Por defecto, el usuario es `root` y el `password` está vacío.

    * Asegúrate de que la conexión a la base de datos sea correcta.

4.  **Iniciar la Aplicación**:

    * Inicia WampServer.

    * Abre tu navegador y ve a `http://localhost/nombre-del-proyecto`.

    * Utiliza el usuario y contraseña iniciales para acceder (Usuario: `admin`, Contraseña: `123654789`).

## 📝 Consideraciones Importantes

* **Seguridad**: El sistema se ha diseñado para un único usuario administrador. Si necesitas más usuarios, la estructura deberá ser adaptada.

* **Bugs y Errores**: Si encuentras algún error o la aplicación no funciona correctamente, puedes revisar la consola del navegador y los logs de WampServer para obtener más detalles.

* **Personalización**: Puedes modificar los archivos de estilo (`assets/css/style.css`) y los archivos de configuración (`configuracion.json`) para adaptar la aplicación a tus necesidades.

## ScreenShots
Vaya a la carpeta [images](images/) para ver todas las capturas de la plataforma

<image src="images/ListaPacientes.jpg" alt="Lista de Pacientes">
<image src="images/historiaClinica.jpg" alt="Lista de Pacientes">
