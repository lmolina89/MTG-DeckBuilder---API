docker build -t mtgdeckbuilder_api .

docker-compose up


sudo docker-compose -f docker-compose.yml up -d

sudo docker exec -it proyectomultimedia-api-docker-lamp_www_1 bash


sudo docker-compose build

sudo docker build -t test .
sudo docker run -it -v /home/ubuntu/proyectomultimedia-api-docker-lamp/mtgdeckbuilderapi.redirectme.net:/certs/ -p 80:80 -p 443:443 --rm test /bin/bash
sudo docker-compose down
sudo docker-compose up
git clone https://gitlab.iesvirgendelcarmen.com/lmolina89/proyectomultimedia-api-docker-lamp.git
sudo docker exec -it proyectomultimedia-api-docker-lamp_www_1 bash
sudo docker logs 5a21bee9ebb3

sudo docker-compose build