# Documentación del proyecto

## Comandos de Docker

1. Para construir una imagen de Docker con el nombre `mtgdeckbuilder_api`, ejecuta el siguiente comando en la terminal:

```cmd
docker build -t mtgdeckbuilder_api .
```

Para levantar los servicios definidos en el archivo docker-compose.yml, utiliza el siguiente comando:

```cmd
docker-compose up
```

Si deseas ejecutar los servicios en segundo plano, utiliza el siguiente comando:

```cmd
sudo docker-compose -f docker-compose.yml up -d
```

Si necesitas acceder al contenedor proyectomultimedia-api-docker-lamp_www_1 y utilizar una shell interactiva, ejecuta lo siguiente:

```cmd
sudo docker exec -it proyectomultimedia-api-docker-lamp_www_1 bash
```

Para construir los servicios definidos en el archivo docker-compose.yml, utiliza el siguiente comando:

```cmd
sudo docker-compose build
```

Para construir una imagen de Docker con el nombre test, ejecuta el siguiente comando:

```cmd
sudo docker build -t test .
```

Si deseas ejecutar el contenedor test en modo interactivo y mapear los puertos 80 y 443, utiliza el siguiente comando:

```cmd
sudo docker run -it -v /home/ubuntu/proyectomultimedia-api-docker-lamp/mtgdeckbuilderapi.redirectme.net:/certs/ -p 80:80 -p 443:443 --rm test /bin/bash
```

Para detener y eliminar los servicios definidos en docker-compose.yml, utiliza el siguiente comando:

```cmd
sudo docker-compose down
```

Para levantar los servicios nuevamente, ejecuta el siguiente comando:

```cmd
sudo docker-compose up
```

Si deseas acceder al contenedor proyectomultimedia-api-docker-lamp_www_1 y utilizar una shell interactiva, ejecuta lo siguiente:

```cmd
sudo docker exec -it proyectomultimedia-api-docker-lamp_www_1 bash
```

Para ver los registros del contenedor con ID 5a21bee9ebb3, utiliza el siguiente comando:

```cmd
sudo docker logs 5a21bee9ebb3
```
