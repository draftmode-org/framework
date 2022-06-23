#Docker
##install
###(1) Install using the repository
[https://docs.docker.com/engine/install/debian/](https://docs.docker.com/engine/install/debian/)  
1. Update the apt package index and install packages to allow apt to use a repository over HTTPS
```
sudo apt-get update
```
```
sudo apt-get install \
ca-certificates \
curl \
gnupg \
lsb-release
```
2. Add Docker’s official GPG key
```
sudo mkdir -p /etc/apt/keyrings
```
```
curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
```
3. Use the following command to set up the repository
```
echo \
"deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
$(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```
###(2) Install Docker Engine
```
sudo apt-get update
```
```
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-compose-plugin
```
##add other user to docker-group
1. Create a docker group if there isn’t one: 
```
sudo groupadd docker
```
2. Add your user to the docker group:
```
sudo usermod -aG docker [non-root user]
```
3. Logout, login again

##docker/nginx test
```
docker run -it --rm -d -p 8080:80 --name web nginx
```
>http://192.57.57.12:8080
###(3) install docker-compose
```
sudo apt update
sudo apt install docker-compose-plugin
```
1. choose version  
[https://github.com/docker/compose/releases](https://github.com/docker/compose/releases)
2. install version
```
sudo curl -L "https://github.com/docker/compose/releases/download/v2.6.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
```
3. set the permission to executable on "docker-compose"
```
sudo chmod +x /usr/local/bin/docker-compose
```
4. test
```
docker-compose --version
```
   