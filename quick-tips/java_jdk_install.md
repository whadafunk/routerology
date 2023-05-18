## Installing JAVA JDK on Debian


Installing Java on a Linux Debian system is a matter of the following steps:


- Download the java jdk archive
- Unpack the archive to a folder of your liking
- Install an update-alternative java pointing to the unpacked java binary
- Configure PATH and JAVA_HOME environment variables



sudo update-alternatives --install /usr/bin/java java /usr/lib/jvm/jdk-16.*/bin/java 1

sudo update-alternatives --config java

java -version

sudo nano /etc/profile.d/java.sh

export PATH=$PATH:/usr/lib/jvm/jdk-11.0.12/bin/
export JAVA_HOME=/usr/lib/jvm/jdk-11.0.12/
source /etc/profile.d/java.sh


