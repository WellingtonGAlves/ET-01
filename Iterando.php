<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function interact($socket)
{
	GLOBAL $fh;
	GLOBAL $command_path;
	GLOBAL $firstInteraction;
	
	GLOBAL $remip;
	GLOBAL $remport;	

	$loopcount = 0;
	$conn_imei = "";
	/* TALK TO YOUR CLIENT */
	$rec = "";
	// Variavel que indica se comando estÃ¡ em banco ou arquivo.
	$tipoComando = "banco"; //"arquivo";
	
	//Checando o protocolo
	$isGIMEI = false;
	$isGPRMC = false;
	
	$send_cmd = "";

	# Read the socket but don't wait for data..
	while (@socket_recv($socket, $rec, 2048, 0x40) !== 0) {
	# If we know the imei of the phone and there is a pending command send it.
   
	# Some pacing to ensure we don't split any incoming data.
		sleep (1);

		# Timeout the socket if it's not talking...
		# Prevents duplicate connections, confusing the send commands
		$loopcount++;
		if ($loopcount > 120) return;

		#remove any whitespace from ends of string.
		$rec = trim($rec);
		
		//Conecta e pega o comando pendente
		$cnx = mysql_connect("localhost", "user1", "pass1") 
		or die("Could not connect: " . mysql_error());
		mysql_select_db('tracker', $cnx);
		
		//mysql_select_db('tracker', $cnx);
		if ($rec != "") 
		{
			mysql_close($cnx);
			if (strpos($rec, "$") === true){
				$isGPRMC = true;
				$loopcount = 0;
				$parts = explode(',',$rec);			
				$cnx = mysql_connect("localhost", "user1", "pass1");
				
				if ($parts[1] !== "" and $parts[0] !== "%") {
				{	
				  if(strpos($rec, 'CEL') == true){
				  	$imei	= substr($parts[0],1);
					$satelliteFixStatus= 'A';
					$latitude = substr($parts[2],1);
					$latitudeHemisphere	= 'S';
					$longitude 	=  substr($parts[3],1);
					$longitudeHemisphere	= 'W';
					$speed 		= $parts[4];
					$gpsSignalIndicator = 'F';
					$infotext  = "tracker";
					$ignicao = $parts[14];
					
					$latitudeDecimalDegrees 	 = "-$latitude";
					$longitudeDecimalDegrees = "-$longitude";

					
				if ($infotext == ""){
				  $infotext = "tracker";	
				  $conn_imei = $imei;
				  	
				  	abrirArquivoLog($conn_imei);
				   printLog($fh, date("d-m-y h:i:sa") . " Connection from $remip:$remport");
					printLog($fh, date("d-m-y h:i:sa") . " Got : $rec");

					mysql_select_db('tracker', $cnx);
					if($gpsSignalIndicator != 'L') {
						$address = null;
						$phone = '7097';
						
						if($parts[14] == 0)
						  $ligado = 'N';
                                        }else{ 
							$ligado = 'S';

						$resLocAtual = mysql_query("select id, latitudeDecimalDegrees, latitudeHemisphere, longitudeDecimalDegrees, longitudeHemisphere from loc_atual where imei = '$imei' limit 1", $cnx);
						$numRows = mysql_num_rows($resLocAtual);
						
						if($numRows == 0){
							mysql_query("INSERT INTO loc_atual (date, imei, phone, satelliteFixStatus, latitudeDecimalDegrees, latitudeHemisphere, longitudeDecimalDegrees, longitudeHemisphere, speed, infotext, gpsSignalIndicator, converte) VALUES (now(), '$imei', '$phone', '$satelliteFixStatus', '$latitudeDecimalDegrees', '$latitudeHemisphere', '$longitudeDecimalDegrees', '$longitudeHemisphere', '$speed', '$infotext', '$gpsSignalIndicator', 0)", $cnx);
						} else {
							mysql_query("UPDATE loc_atual set date = now(), phone = '$phone', satelliteFixStatus = '$satelliteFixStatus', latitudeDecimalDegrees = '$latitudeDecimalDegrees', latitudeHemisphere = '$latitudeHemisphere', longitudeDecimalDegrees = '$longitudeDecimalDegrees', longitudeHemisphere = '$longitudeHemisphere', speed = '$speed', infotext = '$infotext', gpsSignalIndicator = '$gpsSignalIndicator', converte = 0 where imei = '$imei'", $cnx);
						}
                                        }
                                }/* MUDA O STATUS LIGADO / DESLIGADO*/						
                  if ($ignicao == '0') {
                  	mysql_query("UPDATE bem set date = now(), ligado = 'N' WHERE imei = '$imei'",$cnx);	
                  } else { 
               	   mysql_query("UPDATE bem set date = now(), ligado = 'S' WHERE imei = '$imei'",$cnx);
                  
                                          			
						mysql_query("UPDATE bem set date = now(), status_sinal = 'R' WHERE imei = '$imei'",$cnx);
						mysql_query("INSERT INTO gprmc (date, imei, phone, satelliteFixStatus, latitudeDecimalDegrees, latitudeHemisphere, longitudeDecimalDegrees, longitudeHemisphere, speed, infotext, gpsSignalIndicator, address, ligado) VALUES (now(), '$imei', '$phone', '$satelliteFixStatus', '$latitudeDecimalDegrees', '$latitudeHemisphere', '$longitudeDecimalDegrees', '$longitudeHemisphere', '$speed', '$infotext', '$gpsSignalIndicator', '$address','$ligado')", $cnx);
					} 
                                }else{
						mysql_query("UPDATE bem set date = now(), status_sinal = 'S' WHERE imei = '$imei'",$cnx);
				
					# Now check to see if we need to send any alerts.
					if (trim($infotext) != "gprmc")
					{
					   $res = mysql_query("SELECT * FROM bem WHERE imei='$imei'", $cnx);
					   while($data = mysql_fetch_assoc($res)) {
						  switch ($infotext) {
							  case "dt":
								$body = "Disable Track OK";
								break;
							  case "et":
								$body = "Stop Alarm OK";
								break;
							  case "gt";
								$body = "Move Alarm set OK";
								break;
							  case "help me":
								$body = "Help!";
								mysql_query("INSERT INTO message (imei, message) VALUES ('$imei', 'SOS!')", $cnx);
								break;
							  case "ht":
								$body = "Speed alarm set OK";
								break;
							  case "it":
								$body = "Timezone set OK";
								break;
							  case "low battery":
								$body = "Low battery!\nYou have about 2 minutes...";
								mysql_query("INSERT INTO message (imei, message) VALUES ('$imei', 'Bat. Fraca')", $cnx);
								break;
							  case " bat:":
								$body = "Low battery!\nYou have about 2 minutes...";
								mysql_query("INSERT INTO message (imei, message) VALUES ('$imei', 'Bat. Fraca')", $cnx);
								break;
							  case "Low batt":
								$body = "Low battery!\nYou have about 2 minutes...";
								mysql_query("INSERT INTO message (imei, message) VALUES ('$imei', 'Bat. Fraca')", $cnx);
								break;
							  case "move":
								$body = "Move Alarm!";
								mysql_query("INSERT INTO message (imei, message) VALUES ('$imei', 'Movimento')", $cnx);
								break;
							  case "nt":
								$body = "Returned to SMS mode OK";
								break;
							  case "speed":
								$body = "Speed alarm!";
								mysql_query("INSERT INTO message (imei, message) VALUES ('$imei', 'Velocidade')", $cnx);
								break;
							  case "stockade":
								$body = "Geofence Violation!";
								mysql_query("INSERT INTO message (imei, message) VALUES ('$imei', 'Cerca')", $cnx);
								break;
							} //switch
														
							//Enviando e-mail de alerta
							//$headers = "From: $email_from" . "\r\n" . "Reply-To: $email_from" . "\r\n";
							//$responsible = $data['responsible'];
							//$rv = mail($responsible, "Tracker - $imei", $body, $headers);

						} //while
					}
				} else {
					//GRPMC nao precisa reter a sessao
				}
				
				//No protocolo GPRMC cada nova conexÃ£o Ã© um IP. Enviando comando no fim da conexao, apÃ³s obter os dados.
				
				if ($conn_imei != "")
				{
					if ($tipoComando == "banco")
					{
						//Conecta e pega o comando pendente
						$cnx = mysql_connect("localhost", "user1", "pass1")
						or die("Could not connect: " . mysql_error());
						mysql_select_db('tracker', $cnx);
						$res = mysql_query("SELECT c.command FROM command c WHERE c.imei = '$conn_imei' ORDER BY date DESC LIMIT 1");
						while($data = mysql_fetch_assoc($res))
						{
							$send_cmd = $data['command'];
							socket_send($socket, $send_cmd, strlen($send_cmd), 0);
						}
						// Deletando comando
						//$send_cmd = trim($send_cmd);
						//unlink("$command_path/$conn_imei");
						mysql_query("DELETE FROM command WHERE imei = $conn_imei");
						mysql_query("insert into teste(id,string) values(null, '$send_cmd')", $cnx);
						mysql_close($cnx);
						printLog($fh, "Comandos do arquivo apagado: " . $send_cmd . " imei: " . $conn_imei);
					}
					// Comando enviado
					//printLog($fh, date("d-m-y h:i:sa") . " Sent: $send_cmd");
				}

//				if (file_exists("$command_path$conn_imei")) 
//				{
//					$send_cmd = file_get_contents("$command_path$conn_imei");
//					socket_send($socket, $send_cmd, strlen($send_cmd), 0);
//					//mysql_query("DELETE FROM command WHERE imei = $conn_imei");
//					unlink("$command_path$conn_imei");
//					printLog($fh, "Comandos do Banco e Arquivo apagados: " . $send_cmd . " imei: " . $conn_imei);				
//				}
				

/*
				$cnx = mysql_connect("localhost", "user1", "pass1") 
						  or die("Could not connect: " . mysql_error());
				mysql_select_db('tracker', $cnx);
				//$res = mysql_query("SELECT c.command FROM command c WHERE c.command like '**,imei:". $conn_imei .",C,%' and c.imei = $conn_imei ORDER BY date DESC LIMIT 1");
				$res = mysql_query("SELECT c.command FROM command c WHERE c.imei = '$conn_imei' ORDER BY date DESC LIMIT 1");
				while($data = mysql_fetch_assoc($res))
					{
						$send_cmd = $data['command'];
					
						
						
						socket_send($socket, $send_cmd, strlen($send_cmd), 0);
						mysql_query("DELETE FROM command WHERE imei = $conn_imei");
					
					}


				mysql_close($cnx);

*/

				
				break;

			}
		
		//Checando se utilizou os dois protocolos para uma escuta
		if ($isGIMEI == true and $isGPRMC == true) 
		{
			//printLog($fh, "ATENCAO: falha na obtencao do protocolo. Kill pid.");
		}
		

		$rec = "";
	} //while

} //fim interact