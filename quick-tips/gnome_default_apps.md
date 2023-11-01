# Setting the default document viewer in GNOME

If the app does not appear in the list of preferred apps, you need to edit its shortcut under **/var/share/applications**, and add the %F at the end of the exec line as in the example bellow:

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
