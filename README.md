# Login y Registro (Proyecto SO2)

Aplicación PHP + MySQL en Docker para registro y login de usuarios con panel admin.

## Características

- Registro con avatar
- Login, roles `admin`/`usuario`
- Panel de administración (cambia roles, desactiva/elimina usuarios)
- Recuperación de contraseña via token
- Estilos separados: `estilo_general.css` + `admin.css`

## Requisitos

- Docker & Docker-Compose
- Git

## Desarrollo

```bash
git clone git@github.com:TuUsuario/login-registro-so2.git
cd login-registro-so2
docker-compose up -d --build