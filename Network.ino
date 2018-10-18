#include <SPI.h>
#include <Ethernet.h>
#include <SoftwareSerial.h>
#include "SparkFun_UHF_RFID_Reader.h" 

SoftwareSerial softSerial(2, 3);               

RFID nano;                  

# define BUZZER2 9
# define BUZZER1 8

byte mac[] = {0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
byte ip[] = {192,168,1,16};
byte server[] = {192,168,1,2}; 

char tmp[16];

EthernetClient client;

void setup()
{
	Serial.begin(9600);

	pinMode(LED_BUILTIN, OUTPUT);
	pinMode(BUZZER1, OUTPUT);
	pinMode(BUZZER2, OUTPUT);
	digitalWrite(BUZZER2, LOW);

	while (!Serial);          

	if (setupNano(38400) == false)        
	{
	Serial.println(F("Bad wiring."));
	}

	nano.setRegion(REGION_AUSTRALIA);   

	nano.setReadPower(2000);        
	Serial.println(F("Press a key to begin scanning for tags."));
	while (!Serial.available());         
	Serial.read();                 

	nano.startReading();              

	Ethernet.begin(mac, ip);
	Serial.println("Enter any key to continue");
}


boolean setupNano(long baudRate) {
  nano.begin(softSerial);            

  softSerial.begin(baudRate);         
  while(!softSerial);              

  while(softSerial.available()) softSerial.read();
  
  nano.getVersion();

  if (nano.msg[0] == ERROR_WRONG_OPCODE_RESPONSE)
  {
    nano.stopReading();
    Serial.println(F("Module continuously reading. Asking it to stop..."));
    delay(1500);
  }
  else
  {
    softSerial.begin(115200);

    nano.setBaud(baudRate);

    softSerial.begin(baudRate); 
  }
  nano.getVersion();
  if (nano.msg[0] != ALL_GOOD) return (false); 
  nano.setTagProtocol();

  nano.setAntennaPort();

  return (true);
}

void loop() {
	if (nano.check() == true) {
	byte responseType = nano.parseResponse();

	if (responseType == RESPONSE_IS_KEEPALIVE) {
		Serial.println(F("Scanning"));
	}
	else if (responseType == RESPONSE_IS_TAGFOUND) {

		byte tagEPCBytes = nano.getTagEPCBytes(); 
		byte myByteArray[tagEPCBytes];

		if (client.connect(server, 81)) {
			client.print("GET /write_data.php?");
			client.print("id=");
				for (byte x = 0 ; x < tagEPCBytes ; x++) {
          sprintf(tmp, "%.2X", nano.msg[31+x]); 
          Serial.print(tmp);
          client.print(tmp);
				}
				client.println(" HTTP/1.1"); 
				client.println("Host: 192.168.1.2"); 
				client.println("Connection: close"); 
				client.println();
				client.println();
				client.stop();
				Serial.println("Sent byte");

        tone(BUZZER1, 2093, 100);
			}
			else {
				Serial.println("--> connection failed\n");
			}
		}
		else if (responseType == ERROR_CORRUPT_RESPONSE) {
			Serial.println("Bad CRC");
		}
		else {
			Serial.print("Unknown error");
		}
	}
}
