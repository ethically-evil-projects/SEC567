### User Instructions
- Download and extract your payload.

- Customise applet.html with an appropriate pretext.
- Compile the applet.java file:
```
javac applet.java
jar cvf applet.jar applet.class
```
- Sign the applet.jar file with a certificate. In order to generate a self-signed certificate:
```
keytool -genkey -keystore mykeystore -alias payload
keytool -selfcert -keystore mykeystore -alias payload
```
- Then sign the applet.jar file with the certificate:
```
jarsigner -keystore mykeystore applet.jar payload
```
