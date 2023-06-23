# Documentación del proyecto

## Comandos de Docker

Si deseas ejecutar los servicios en segundo plano, utiliza el siguiente comando:

```cmd
sudo docker-compose -f docker-compose.yml up -d
```

Si necesitas acceder al contenedor proyectomultimedia-api-docker-lamp_www_1 y utilizar una shell interactiva, ejecuta lo siguiente:

```cmd
sudo docker exec -it tfg-mtg-deckbuilder-api_www_1 bash
```

Para detener y eliminar los servicios definidos en docker-compose.yml, utiliza el siguiente comando:

```cmd
sudo docker-compose down
```

Parar todos los contenedores de docker
```cmd
sudo docker stop $(sudo docker ps -aq)

```

Eliminar todas las imagenes, volumenes, contenedores, networks y builds
```cmd
sudo docker system prune --all --force
```


Para ver los registros del contenedor con ID 5a21bee9ebb3, utiliza el siguiente comando:

```cmd
sudo docker logs 5a21bee9ebb3
```

cuando se generan los certificados SSL, se crean dentro de la ruta

```cmd
/etc/letsencrypt/live/mtgdeckbuilderapi.redirectme.net/
```

