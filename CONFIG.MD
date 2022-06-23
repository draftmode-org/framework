#setup VM + Debian
##(1) create vm instance (step 1)
Virtual Machine
- Name: Debian
- Type: Linux
- Version: Debian (64-bit)
- Memory: 16M (minimum)
- Create Disc: yes
- [CREATE]

Virtual Disc
- Space: 30GB (minimum)
- Disc Type: VDI
- dynamic
- [CREATE]

Setup/Join ISO [Instance Settings]
- Massenspeicher
  - Controller: IDE
    - empty
    - => choose ISO File
    - [x] Live-CD/DVD
- [START]

##(2) configure debian
- [INSTALL] (!graphical installation)
- [PRESS 3 TIME ENTER] (no matter about LANGUAGE,... we use ssh to work with the instance)
- Hostname: optional
- Domain name: [ENTER]
- Root password: [2 x ENTER] (dont set a password)
- Full name for new user: [ENTER]
- Username for your account: **{user}** (your joice)  
- Choose a password for new user: **{pwd}** (your joice)
- Time Zone: [ENTER, or choose]
- Partitioning method: Guided - use entire disk
- Select disk to partition: [ENTER]
- Partitioning scheme: All files...
- Partition disks: Finish....
- Write the changes to disk: <Yes>
- Configure the package manager: <No>
  - mirror country: (choose country next to you: fetch repositories, ...)
  - archive mirror: deb.debian.org
  - Http proxy: [ENTER]
- Configuring pop...: <No>
- Software selections (take care about ENTER and SPACE to activate/deactivate)
  - [*] SSH Server
  - [*] standard system utilities
  - [ENTER]
- Install the GRUB
  - boot loader: <YES>
  - device: /dev/sda
- Install complete <Continue>
> shut down instance

##(3) create Ethernet Host-Only adapter (if not exists)
[VM / Host Only Network Manager]
use created/set ip address(should end with .1)

##(4) create vm instance (step 2) - remove ISO
remove ISO [Instance Settings]
- Massenspeicher
  - Controller: IDE
    - debian...iso
    - [REMOVE, ICON on bottom]
  
##(5) set instance network
[Instance Settings]
- Adapter 1: NAT
- Adapter 2: Host-Only Adapter
  - (use created Ethernet Host-Only adapter)
- [OK]
>start instance

##(6) configure debian network/interfaces
```sudo nano /etc/network/interfaces```
address: ip to reach instance from outside  
_not allowed to be identically to the Host-Only-Adapter IP_   
_has to be in the same network range from Host-Only-Adapter_
Example:  
**{outsideIP}: 192.57.57.12**
```
allow-hotplug enp0s8
iface enp0s8 inet static
address 192.57.57.12
netmask 255.255.255.0
```
>$ sudo reboot

##(7) configure sharded folder
### instance settings
>stop instance

[Instance Settings]  
[Shared Folder]
- add
  - Path-Folder: (source)
  - Path-Name: {sharedName} automatically set after choosing source)
  - [*] automatically bind
  - mounting point: {bindInsideFolder}
>start instance
### inside instance
```
sudo mkdir {bindInsideFolder}
sudo mount -t vboxsf {sharedName} {bindInsideFolder}
sudo nano /etc/fstab
```
add
```
{sharedName} {insideFolderName} vboxsf defaults 0 0
```
>$ sudo reboot

#From outside
##connect by SSH
```
ssh {user}@{outsideIP}
```
example
```
ssh debian@192.57.57.12
```

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
   