#include <SPI.h>
#include <Ethernet.h>

byte mac[] = {0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
byte ip[] = {192,168,1,16};
byte server[] = {192,168,1,2}; 

int reading;

// Initialize the Ethernet server library
EthernetClient client;

void setup() {
  Ethernet.begin(mac, ip);
  Serial.begin(9600);
  Serial.println("Enter any key to continue");
}

void loop() {
  while (!Serial.available())
  
  reading = random(20);
   
  if (client.connect(server, 81)) {
    client.print("GET /write_data.php?"); // This
    client.print("value="); // This
    client.print(reading); // And this is what we did in the testing section above. We are making a GET request just like we would from our browser but now with live data from the sensor
    client.println(" HTTP/1.1"); // Part of the GET request
    client.println("Host: 192.168.1.2"); // IMPORTANT: If you are using XAMPP you will have to find out the IP address of your computer and put it here (it is explained in previous article). If you have a web page, enter its address (ie.Host: "www.yourwebpage.com")
    client.println("Connection: close"); // Part of the GET request telling the server that we are over transmitting the message
    client.println(); // Empty line
    client.println(); // Empty line
    client.stop();    // Closing connection to server
    Serial.println("Sent byte");
  }
  else {
    Serial.println("--> connection failed\n");
  }

  delay(5000);
}
