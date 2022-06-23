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
