# Agenda Personal de Contactos con Autenticación en PHP y MySQL

## 1. Descripción del proyecto
Aplicación web desarrollada en PHP y MySQL en la que se implementa mecanismos de autenticación de usuarios, un manejo seguro de sesiones y la gestión de una agenda personal de contactos. El sistema garantiza el aislamiento de la información mediante controles en el servidor para que cada usuario visualice y administre exclusivamente sus propios contactos, impidiendo el acceso cruzado incluso ante modificaciones manuales de la URL.

## 2. Tecnologías utilizadas
* **Lenguaje de servidor:** PHP 8.x (acceso a datos mediante extensión PDO y sentencias preparadas).
* **Gestor de base de datos:** MySQL / MariaDB.
* **Frontend:** HTML5, CSS3 y JavaScript.
* **Entorno de desarrollo y servidor local:** Visual Studio Code y XAMPP (Apache + MySQL).

## 3. Requisitos para su ejecución
* Servidor local **XAMPP** (o equivalente) con los servicios **Apache** y **MySQL** activos.
* PHP 7.4 o superior (recomendado PHP 8.x).
* Navegador web (Chrome, Firefox, Edge).

## 4. Pasos para instalar la aplicación
1. **Clonar o descargar el proyecto:** Coloque la carpeta `agenda_contactos` dentro del directorio raíz de su servidor web local (en XAMPP: `C:\xampp\htdocs\agenda_contactos`).
2. **Iniciar servicios:** Abra el panel de control de XAMPP e inicie los módulos **Apache** y **MySQL**.
3. **Importar la base de datos:**
   * Ingrese a `http://localhost/phpmyadmin` desde su navegador.
   * Diríjase a la pestaña **Importar** y ejecutar el archivo `database.sql` incluido en la raíz del proyecto. Esto permite crear automáticamente la base de datos `agenda_contactos` junto con las tablas `usuarios` y `contactos`.
4. **Configurar credenciales (opcional):** Si su servidor MySQL utiliza una contraseña diferente a la predeterminada de XAMPP, actualice las variables `$username` y `$password` en el archivo `conexion.php`.
5. **Ejecutar la aplicación:** Abra su navegador web e ingrese a `http://localhost/agenda_contactos/login.php` o `http://localhost/agenda_contactos/registro.php`.
