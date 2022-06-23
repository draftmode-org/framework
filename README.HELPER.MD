#like an FAQ
###connect to VM by SSH
```
ssh {user}@{outsideIP}
```
example
```
ssh debian@192.57.57.12
```

###docker-compose
execute docker-compose.yml  
>docker-compose up

###docker
build image and tag built image
>docker build -t {buildTagName} -f {DockerFile} . 

php: jump into container / shell command line
>docker run --name {containerName} --rm -i -t {buildTagName} sh
