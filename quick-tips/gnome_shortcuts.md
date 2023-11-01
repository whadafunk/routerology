# Creating application shortcuts in GNOME

Usually the application shortcut is registered with the system by the installation scripts,  
but there are cases when you need to create it manually.


The shortcuts for installed applications are stored as text-files under **/usr/share/applications**.  
You will find there some files with *.desktop* extension.

Use the example bellow as a template to create your own:

```
[Desktop Entry]
Version=1.0
Type=Application
Terminal=false
Exec=/home/daniel/lorien/Lorien.x86_64
Name=lorien
Comment=WhiteBoard
Icon=/usr/share/icons/hicolor/32x32/apps/rygel.png
Categories=utility
```


There is another tidbit related to this.  
If you want your application to appear in the list of default apps (for opening a document), then you need  
to add an *%F* at the end of the *Exec* stanza. See example bellow:


```
[Desktop Entry]
Name=Adobe Reader 9
MimeType=application/pdf;application/vnd.fdf;application/vnd.adobe.pdx;application/vnd.adobe.xdp+xml;application/vnd.adobe.xfdf;
Exec=acroread  %F
Type=Application
GenericName=PDF Viewer
Terminal=false
Icon=AdobeReader9
Caption=PDF Viewer
X-KDE-StartupNotify=false
Categories=Application;Office;Viewer;X-Red-Hat-Base;
InitialPreference=9
```




