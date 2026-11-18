# Búho.lib — nueva versión

1. Haz una copia de seguridad de tu proyecto y de la base de datos.
2. Copia esta carpeta a `htdocs/Buho.lib` (o ajusta la ubicación de tu servidor PHP).
3. En MySQL Workbench ejecuta `database/auth_update.sql` sobre la base `buho_lib` existente. Si aún no la creaste, importa primero `database/schema.sql`.
4. En una terminal ubicada en la carpeta del proyecto ejecuta: `php crear_admin.php "Administrador Principal" "admin@ejemplo.com"` y escribe una contraseña segura (mínimo 10 caracteres). No ejecutes ese archivo en el navegador.
5. Abre `http://localhost/Buho.lib/?pagina=login`. Los clientes pueden registrarse desde `?pagina=registro`.
6. Los clientes existentes en la tabla `lectores` NO tienen contraseña todavía. Para habilitarles una cuenta, hay que crearles credenciales mediante un flujo administrativo posterior; no se asignan contraseñas predeterminadas.

Nota: se genera un documento interno `WEB-...` para los clientes que se registran, sin pedirles números. Si tu proceso requiere documentos de identidad reales, adapta ese registro. El administrador conserva la gestión de préstamos y libros; el cliente puede ver el catálogo y sus propios préstamos.
