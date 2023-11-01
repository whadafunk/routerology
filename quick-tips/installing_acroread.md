# Installing Adobe Acrobat Reader in Linux Debian

# Repository automated installation

The easiest way would be to take the package for Linux Mint Debian Edition (LMDE) and install that instead. 
LMDE is based on and 100% compatible with Debian testing.  
You can safely mix LMDE and Debian repositories on a single system.

So, since LMDE packages acroread, you can install it by adding their repo to your /etc/apt/sources.list:

deb http://debian.linuxmint.com/latest/multimedia testing main non-free

Once you have added that line, update the sources and install:

sudo apt-get update
sudo apt-get install acroread

### Manual installation of the x386 version


*This is a version for a legacy arhitecture, but I managed to install it on Debian 11*


sudo dpkg --add-architecture i386
sudo apt-get update
sudo apt-get install libxml2:i386

cd ~/Downloads
sudo dpkg -i  AdbeRdr9.5.5-1_i386linux_enu.deb
sudo apt-get -f install
acroread


sudo apt-get install libgdk-pixbuf2.0-0:i386





