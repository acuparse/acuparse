# Acuparse RaspberryPi Direct Connection Guide

This is a **community provided** guide moved into the docs from the Wiki. It was designed to help you with installing
Acuparse on a RaspberryPi when using an Access or SmartHUB directly connected to the Pi.

!!! warning If you are not connecting to your Access or SmartHUB directly, use the
Raspbian [Light](https://downloads.raspberrypi.org/raspbian_lite_latest)
image for your install. Then follow
the [Automated Installation Guide](https://docs.acuparse.com/INSTALL/#bare-metal-or-virtual).

## What is a RaspberryPi

RaspberryPi's are a small single-board microcomputer, that is fairly inexpensive (most kits are under 40USD) and are
powerful enough to run Acuparse sufficiently. The OS used in this document will be Raspbian Bullseye (a version of Debian
Linux).

The two current versions of RaspberryPi that are available are the RaspberryPi Zero W (RPi0W) and the RaspberryPi 3 (
RPi3). While the RPi0W will work, it's not recommended for performance reasons and limited ports (a single MicroUSB port
and built-in WiFi). The RPi3 performs very well, and also provides 4 standard USB 2.0 ports and a built-in 10/100 Mbps
Ethernet port and WiFi.

## Overview of the setup

### Parts List

- RaspberryPi 3
- MicroUSB power supply (2.1A or higher, a powered USB hub can also work)
- Case (Optional but recommended)
- MicroSD card (16GB or 32GB, Class 10 recommended)
- MicroSD to SD card adapter
- USB Ethernet adapter (optional if you want a wired connection to your router instead of WiFi)

> **Note:** MicroSD cards are not created equal. Choose a fast performing card to use with Acuparse.
> It does a large amount of reading and writing!
> See [microSD Card Benchmarks](http://www.pidramble.com/wiki/benchmarks/microsd-cards) for assistance selecting the
> right MicroSD card.

In addition to the above parts, you will need an Acurite SmartHub or Acurite Access, and a computer with an SD card
reader (either built-in or a USB adapter. This is for the initial set up of the MicroSD card).

NOTE: RPi3 kits can be purchased from sites such as Amazon that include the Pi, power supply, case, and even MicroSD
card. Here are some example Amazon links for the items above:

- [RaspberryPi 3 Kit](http://a.co/hE9ceb2) - Includes Pi, Power Supply, and Case
- [32 GB MicroSD with SD adapter](http://a.co/aHlZaCZ)
- [USB Ethernet Adapter](http://a.co/7SyO97Z) - This is optional if you want a wired connection to your router instead
  of WiFi

If you decide to use a Pi Zero W instead of a Pi 3 (not recommended as noted above), then you will have to have
a [MicroUSB Ethernet Adapter](http://a.co/41NxQsi) like this to connect your SmartHub / Access as described in this
project.

### Hardware set up description

The SmartHub or Access will be connected directly to the RPi3 via an ethernet cable to the RPI3's built-in ethernet
port. The RPi3 will then connect to your local LAN either via the built-in WiFi, or via a wired ethernet cable to your
router using a USB Ethernet adapter. The RPi3 will be "headless" (i.e. no monitor, keyboard, or mouse attached) in this
setup.

## Installation

### Install Raspbian Buster onto the MicroSD

Follow this guide for setting up your MicroSD
card: [How to Install or Upgrade to Raspbian Bullseye](https://howchoo.com/pi/raspbian-buster-install-or-upgrade)
. The Buster install directions are still valid for the Bullseye release. Use the [Light](https://downloads.raspberrypi.org/raspbian_lite_latest)
image for your installation. When completing your wpa_supplicant file, add a new line with `scan_ssid=1` that is between the `ssid=` and `psk=` lines
.

If you are going to connect your Pi directly with a wired network cable (not using WiFi), then you can skip the wpa_supplicant file creation
altogether. Also, if you are using a Windows computer to connect to your Pi, you will need
to [Download PuTTY](https://www.chiark.greenend.org.uk/~sgtatham/putty/latest.html) for the terminal program to access
it via SSH. For most modern PC's you will choose the 64bit MSI Windows Installer, and then double click / run the
downloaded file to install PuTTY.

### Pi networking setup

Now we need to do some setup of the networking on the Pi. In a terminal window connected to your Pi, do the following
command:

```bash
sudo apt-get update && sudo apt-get -y install dnsmasq iptables-persistent
```

A couple of screens will come up during the iptables-persistent part of the install. Choose "No" (use the tab key to
select the NO option) on each of these and hit enter to confirm.

At this point, make sure your SmartHub / Access is powered on and connected to your Pi via a network cable to the
built-in ethernet port. Also, if you are going to use a wired connection for your LAN, make sure the ethernet adapter is
connected to your Pi and a network cable is connected and attached to your local network.

Now, we need to determine the network adapter names. Run the following command on your Pi:

```bash
ip addr
```

Look at the left-most column for the names of your adapters. Wired network adapters will have names similar to eth0 and
eth1. WiFi will have a name similar to wlan0. (NOTE: Depending on initial configuration, sometimes the names will be
longer and include the MAC address for the device). In our set up, eth0 will be the built-in network port on our Pi and
where we will plug in the SmartHub or Access. If you are using WiFi to connect to your LAN, then wlan0 will be for your
LAN connection. If you are using an ethernet adapter, then that should be like eth1 for your LAN connection.

Now issue the following commands:

```bash
sudo mv /etc/dnsmasq.conf /etc/dnsmasq.conf.orig  
sudo nano /etc/dnsmasq.conf
```

This will put you into a new file you are creating. Now copy and paste the following:

```bash
no-resolv
server=8.8.8.8 # Use Google DNS
interface=eth0 # Use interface eth0  
no-dhcp-interface=wlan0
dhcp-range=eth0,192.168.6.50,192.168.6.100,255.255.255.0,24h # IP range and lease time
```

If you're using wired LAN, change the 4th line to:

`no-dhcp-interface=eth1`

Hit Ctrl-X to exit and choose Y and hit enter on the file name that comes up.

Now do the following:

```bash
sudo nano /etc/dhcpcd.conf
```

Go to the bottom of the file and add the following lines. You will want to make sure that the first two static lines,
match your LAN address for your router. If your router is not 192.168.1.1 you will need to change both of those lines so
that the address of your Pi and your Router address are in the same subnet (usually 192.168.0.x or 192.168.1.x for most
routers). Also, if you are using a wired network adapter then change the line with wlan0 to eth1. Don't change the
static IP under the eth0 line (this is setting up a new network for the Acurite Hub device). Then Ctrl-X to save your
changes.

```bash
# Home LAN  
interface wlan0 # Change to eth1 if using wired adapter  
static ip_address=192.168.1.70 # Set the Pi IP address  
static routers=192.168.1.1 # Your router's IP
#
# SmartHub / Access network  
interface eth0
static ip_address=192.168.6.1/24
```

Now we need to edit the hosts file:

```bash
sudo nano /etc/hosts
```

At the bottom of the hosts file add the line for whichever Acurite device you are using:

```bash
192.168.1.70   hubapi.myacurite.com # If you are using a SmartHub  
192.168.1.70   atlasapi.myacurite.com # If you are using an Access
```

NOTE that the IP needs to match what you set for the Pi IP address in the dhcpcd.conf file previously.

Now run the following commands (if you are using a wired connection for your LAN, change wlan0 to eth1 in the 4th and
6th command:

```bash
sudo iptables -F  
sudo iptables -X  
sudo iptables -t nat -F  
sudo iptables -A FORWARD -o wlan0 -i eth0 -s 192.168.0.0/24 -m conntrack --ctstate NEW -j ACCEPT  
sudo iptables -A FORWARD -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT
sudo iptables -t nat -A POSTROUTING -o wlan0 -j MASQUERADE
sudo netfilter-persistent save
```

At this point, the editing and network configuration is all complete. To make sure there are no issues, do the following
commands:

```bash
sudo systemctl restart networking.service
sudo systemctl restart dnsmasq
```

If there are no errors, reboot your Pi as follows:

```bash
sudo reboot now
```

Wait a minute or two for the Pi to reboot. Now when you ssh to it, you will be connecting to the static IP you set up
for it (in the above edits, it would have been 192.168.1.70 unless you changed this).

### Acuparse Installation

Run the following commands:

```bash
cd ~  
curl -O https://gitlab.com/acuparse/installer/raw/master/install && sudo bash install | tee ~/acuparse.log`  
```

If that fails, try:

```bash
wget https://gitlab.com/acuparse/installer/raw/master/install && sudo bash install | tee ~/acuparse.log`
```

You will be prompted through the installation for passwords and such. As long as there are no errors after the
installation completes, you will need to open a browser to point to your Pi's address (example: `http://192.168.1.70`).
Then follow the prompts to complete the setup of your Acuparse environment.
